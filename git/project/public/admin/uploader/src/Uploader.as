/**
 * Flash Uploader
 *
 * @author    kakalong {@link http://yanbingbing.com}
 * @version   $Id: Uploader.as 5370 2012-04-25 07:06:11Z kakalong $
 */
package 
{
	import org.FileQueue;
	import org.events.TriggerEvent;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.events.*;
	import flash.external.ExternalInterface;
	import flash.net.FileFilter;
	import flash.net.FileReference;
	import flash.net.FileReferenceList;
	import flash.utils.describeType;
	import flash.utils.Timer;
	import flash.system.Security;
	
	public class Uploader extends Sprite 
	{
		private var _params:Object;
		private var _jsonType:Boolean;
		private var _fileQueue:FileQueue;
		private var _setupTimer:Timer = new Timer(500, 0);
		
		public function Uploader():void {
			Security.allowDomain("*");
            Security.allowInsecureDomain("*");
			stage.align = StageAlign.TOP_LEFT;
			
			_params = root.loaderInfo.parameters;
			_params.script = decodeURIComponent(_params.script);
			
			_jsonType = _params.jsonType == '1';
			
			_setupTimer.addEventListener(TimerEvent.TIMER, setupExternalInterface);
			_setupTimer.start();
			stage.stageWidth > 0 && setupExternalInterface();
		}
		
		private function init():void {
			var button:Sprite = new Sprite();
			button.graphics.beginFill(0, 0);
			button.graphics.drawRect(0, 0, Math.max(stage.stageWidth, 20), Math.max(stage.stageHeight, 10));
			button.graphics.endFill();
			button.buttonMode = true;
			addChild(button);
			
			var allowedTypes:Array = [];
			if (_params.fileExt) {
				var fileExts:Array = decodeURIComponent(_params.fileExt).split('|'),
					fileDesc:Array = _params.fileDesc
						? decodeURIComponent(_params.fileDesc).split('|')
						: [];
				for (var n:Number = 0; n < fileExts.length; n++) {
					allowedTypes.push(new FileFilter(fileDesc[n] ? (fileDesc[n]+'('+fileExts[n]+')') : fileExts[n], fileExts[n]));
				}
			}
			
			if (_params.multi) {
				var selectList:FileReferenceList = new FileReferenceList();
				selectList.addEventListener(Event.SELECT, function():void{
					selectFiles(selectList.fileList);
				});
				addEventListener(MouseEvent.CLICK, function():void{
					if (!_fileQueue.running) {
						selectList.browse(allowedTypes);
					}
				});
			} else {
				var selectOne:FileReference = new FileReference();
				selectOne.addEventListener(Event.SELECT, function():void{
					clearQueue();
					selectFiles([selectOne]);
				});
				addEventListener(MouseEvent.CLICK, function(){
					if (!_fileQueue.running) {
						selectOne.browse(allowedTypes);
					}
				});
			}
			
			_fileQueue = new FileQueue(_params.script, _params.fieldName || 'Filedata',
				Number(_params.sizeLimit), uint(_params.queueLengthLimit));
			
			_fileQueue.addEventListener(TriggerEvent.SELECT_START, trigger);
			_fileQueue.addEventListener(TriggerEvent.SELECT_ONE, trigger);
			_fileQueue.addEventListener(TriggerEvent.SELECT_END, trigger);
			_fileQueue.addEventListener(TriggerEvent.UPLOAD_START, trigger);
			_fileQueue.addEventListener(TriggerEvent.UPLOAD_PROGRESS, trigger);
			_fileQueue.addEventListener(TriggerEvent.UPLOAD_COMPLETE, trigger);
			_fileQueue.addEventListener(TriggerEvent.UPLOAD_CANCEL, trigger);
			_fileQueue.addEventListener(TriggerEvent.QUEUE_START, trigger);
			_fileQueue.addEventListener(TriggerEvent.QUEUE_COMPLETE, trigger);
			_fileQueue.addEventListener(TriggerEvent.QUEUE_SUCCESS, trigger);
			_fileQueue.addEventListener(TriggerEvent.QUEUE_FULL, trigger);
			_fileQueue.addEventListener(TriggerEvent.QUEUE_CLEAR, trigger);
			_fileQueue.addEventListener(TriggerEvent.ERROR, trigger);
		}
		
		private function testExternalInterface():void {
			_setupTimer.stop();
			_setupTimer.removeEventListener(TimerEvent.TIMER, setupExternalInterface);
			_setupTimer = null;
			init();
		}
		
		private function setupExternalInterface(e:TimerEvent = null):void {
			try {
				ExternalInterface.addCallback('startUpload', startUpload);
				ExternalInterface.addCallback('cancelUpload', cancelUpload);
				ExternalInterface.addCallback('clearQueue', clearQueue);
				ExternalInterface.addCallback('testExternalInterface', testExternalInterface);
			} catch (e:Error) {return;}
			ExternalInterface.call('Uploader.testExternalInterface("' + _params.guid + '")');
		}
		
		private function selectFiles(list:Array):void {
			trigger(new TriggerEvent(TriggerEvent.SELECT_START));
			for (var n:uint = 0, l:uint = list.length; n < l; n++) {
				if (!_fileQueue.addFile(list[n])) {
					break;
				}
			}
			trigger(new TriggerEvent(TriggerEvent.SELECT_END, {
				fileCount:_fileQueue.queueLength,
				allBytesTotal:_fileQueue.allBytesTotal
			}));
		}
		
		private function startUpload(id:String = null):void {
			var scriptData:String = decodeURIComponent(ExternalInterface.call('eval', 'Uploader.readParams("' + _params.guid + '")') as String);
			scriptData && _fileQueue.variables.decode(scriptData);
			_fileQueue.variables['HTTP_COOKIE'] = ExternalInterface.call('eval', '(function(){return document.cookie;})()');
			if (_jsonType) {
				_fileQueue.variables['HTTP_ACCEPT'] = "application/json,application/javascript";
			}
			_fileQueue.upload(id);
		}
		
		private function cancelUpload(id:String):void {
			_fileQueue.cancel(id);
		}
		
		private function clearQueue():void {
			_fileQueue.clear();
		}
		
		CONFIG::DEBUG private function console(...rest):void {
			ExternalInterface.call('console.info('+convertToString(rest)+')');
		}
		
		private function trigger(event:TriggerEvent):void {
			ExternalInterface.call('Uploader.trigger("'+ _params.guid + '", "' + event.type + '", ' + convertToString(event.args) + ')');
		}
		
		private function convertToString( value:* ):String {
			
			if ( value is String ) {
				
				return escapeString( value as String );
				
			} else if ( value is Number ) {
				
				return isFinite( value as Number) ? value.toString() : "null";

			} else if ( value is Boolean ) {
				
				return value ? "true" : "false";

			} else if ( value is Array ) {
			
				return arrayToString( value as Array );
			
			} else if ( value is Object && value != null ) {
			
				return objectToString( value );
			}
            return "null";
		}
		
		private function escapeString( str:String ):String {
			var s:String = "";
			var ch:String;
			var len:Number = str.length;
			
			for ( var i:int = 0; i < len; i++ ) {
			
				ch = str.charAt( i );
				switch ( ch ) {
				
					case '"':
						s += "\\\"";
						break;
						
					case '\\':
						s += "\\\\";
						break;
						
					case '\b':
						s += "\\b";
						break;
						
					case '\f':
						s += "\\f";
						break;
						
					case '\n':
						s += "\\n";
						break;
						
					case '\r':
						s += "\\r";
						break;
						
					case '\t':
						s += "\\t";
						break;
						
					default:
						if ( ch < ' ' ) {
							var hexCode:String = ch.charCodeAt( 0 ).toString( 16 );
							
							var zeroPad:String = hexCode.length == 2 ? "00" : "000";
							
							s += "\\u" + zeroPad + hexCode;
						} else {
							s += ch;
							
						}
				}
				
			}
						
			return "\"" + s + "\"";
		}
		
		private function arrayToString( a:Array ):String {
			var s:String = "";
			
			for ( var i:int = 0; i < a.length; i++ ) {
				if ( s.length > 0 ) {
					s += ","
				}
				
				s += convertToString( a[i] );	
			}
			
			return "[" + s + "]";
		}
		
		private function objectToString( o:Object ):String {
			var s:String = "";
			
			var classInfo:XML = describeType( o );
			if ( classInfo.@name.toString() == "Object" ) {
				var value:Object;
				
				for ( var key:String in o ) {
					value = o[key];
					
					if ( value is Function ) {
						continue;
					}
					
					if ( s.length > 0 ) {
						s += ","
					}
					
					s += escapeString( key ) + ":" + convertToString( value );
				}
			} else {
				for each ( var v:XML in classInfo..*.( name() == "variable" || name() == "accessor" ) )
				{
					if ( s.length > 0 ) {
						s += ","
					}
					
					s += escapeString( v.@name.toString() ) + ":" + convertToString( o[ v.@name ] );
				}
				
			}
			
			return "{" + s + "}";
		}
	}
}