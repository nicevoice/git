/**
 * $Id: editor_plugin_src.js 2009-05-05  $
 *
 * @author Ywindf
 * @modified by shanhuhai 2010.7.9
 */

(function() {

	tinymce.create('tinymce.plugins.OneKeyClearPlugin', {
		init : function(ed, url) {
			//注册一键排版
			ed.addCommand('mceOneKeyClear', function() {
				// 递归遍历dom元素
				var parseElement = function(obj) {
					if (typeof (obj) == 'object' && obj.nodeType == 1) {
						var elmTagName = obj.tagName.toLocaleLowerCase();
						obj = replaceElement(obj);
						if (obj == null) {
							return;
						}
					}
					if (obj.hasChildNodes()) {
						parseElement(obj.firstChild);
					}
					if (obj.nextSibling != null) {
						parseElement(obj.nextSibling);
					}
				}

				// 格式化dom元素属性
				var replaceElement = function(obj) {
					var newObj;
					var elmTagName = obj.tagName.toLocaleLowerCase();
					var elmStyle = obj.getAttribute('data-mce-style') || '';
					var elmClassName = obj.getAttribute('class') || obj.getAttribute('className') || '';
					var elmType = obj.getAttribute('data-mce-type') || '';
					var copyAttribute = function(obj, elmClassName, elmStyle) {
						obj.setAttribute('class', elmClassName);
						obj.setAttribute('className', elmClassName);
						obj.setAttribute('style', elmStyle);
						obj.setAttribute('data-mce-style', elmStyle);
					};
					var copyChildElm = function(newObj, obj) {
						var arr = obj.childNodes;
						if (arr.length) {
							for (var item,length=arr.length,i=0; i<length; i++) {
								item = arr[i];
								newObj.appendChild(item.cloneNode(1));
							}
						}
					};
					var isTag = function(name, tag) {
						var reg = new RegExp('\\s(' + tag.join('|') + ')\\s');
						return reg.test(' ' + name + ' ');
					}
					// 设置不清理标志
					if (elmType == 'forbid') {
						return obj;
					}
					// 干掉允许之外的标签
					if (!isTag(elmTagName, ['font','a','p','div','br','img','b','strong','span','h[1-6]','em','table','tr','td','th','thead','tbody','ul','li'])) {
						obj.parentNode.removeChild(obj);
						return null;
					}
					trimFront(obj);
					if (isTag(elmTagName, ['p','br','li'])) {
						// 将p,li,br转换为p标签,清除非法属性
						newObj = document.createElement('p');
						copyAttribute(newObj, elmClassName, elmStyle);
						copyChildElm(newObj, obj);
						obj.parentNode.replaceChild(newObj, obj);
					} else if (isTag(elmTagName, ['div','ul'])) {
						newObj = document.createElement('p');
						newObj.innerHTML = '&nbsp;';
						obj.parentNode.insertBefore(newObj, obj);
						if (obj.hasChildNodes()) {
							for (var length=obj.childNodes.length,i=0; i<length; i++) {
								var item = obj.childNodes[i].cloneNode(1);
								obj.parentNode.insertBefore(item, obj);
							}
						}
						obj.parentNode.removeChild(obj);
					} else if (isTag(elmTagName, ['img'])) {
						// 让图片外层为一个居中的p标签
						if (obj.parentNode.tagName.toLocaleLowerCase() != 'p' || obj.previousSibling != null || obj.nextSibling != null) {
							var p = document.createElement('p');
							p.style.textAlign = 'center';
							p.setAttribute('data-mce-style', 'text-align:center;');
							p.appendChild(obj.cloneNode(1));
							obj.parentNode.replaceChild(p, obj);
							// 为了正确遍历img后一个元素这里指向一个空节点
							newObj = document.createTextNode('');
							p.parentNode.insertBefore(newObj, p.nextSibling);
						} else {
							var parentElmStyle = obj.parentNode.getAttribute('data-mce-style') || '';
							if (!/\s+text-align\s+/.test(' ' + parentElmStyle + ' ')) {
								parentElmStyle = parentElmStyle + ' ;text-align:center;';
							} else {
								parentElmStyle = parentElmStyle.replace(/text-align\s*:\s*\w+/, 'text-align:center');
							}
							obj.parentNode.setAttribute('data-mce-style', parentElmStyle);
							newObj = obj;
						}
					} else if (isTag(elmTagName, ['b','strong','h[1-6]'])) {
						// b,strong,h*标签统一转换为strong
						newObj = document.createElement('strong');
						copyAttribute(newObj, elmClassName, elmStyle);
						copyChildElm(newObj, obj);
						obj.parentNode.replaceChild(newObj, obj);
					} else if (isTag(elmTagName, ['table','tr','th','td','tbody','thead'])) {
						// 保存table
						newObj = obj;
					} else  {
						if (obj.hasChildNodes()) {
							for (var length=obj.childNodes.length,i=0; i<length; i++) {
								var item = obj.childNodes[i].cloneNode(1);
								newObj || (newObj = item);
								obj.parentNode.insertBefore(item, obj);
							}
							obj.parentNode.removeChild(obj);
							newObj = newObj.previousSibling || newObj.parentNode;
						} else {
							newObj = obj.nextSibling || null;
							obj.parentNode.removeChild(obj);
							newObj = newObj ? newObj.previousSibling || newObj.parentNode : null;
						}
					}
					return newObj;
				}

				// 处理空白p标签
				var fuckP = function(obj) {
					var parentObj = obj.parentNode;
					var prevObj = obj.previousSibling;
					var nextObj = obj.nextSibling;
					if (obj.tagName.toLocaleLowerCase() != 'p') {
						return obj;
					}
					if (obj.innerHTML == '' || obj.innerHTML == '&nbsp;') {
						parentObj.removeChild(obj);
						return (prevObj == null && nextObj == null) ? fuckP(parentObj) : (prevObj || parentObj || null);
					}
					return obj;
				}

				// 格式化table标签
				var parseTable = function(obj) {
					var copyChildElm = function(newObj, obj) {
						var arr = obj.childNodes;
						if (arr.length) {
							for (var item,length=arr.length,i=0; i<length; i++) {
								item = arr[i];
								newObj.appendChild(item.cloneNode(1));
							}
						}
					};
					var newObj = obj.previousSibling || obj.parentNode || null;
					for (var tr,trs=obj.getElementsByTagName('tr'),i=0,trlength=trs.length; i<trlength; i++) {
						tr = trs[i];
						var p = document.createElement('p');
						for (var td,tds=tr.getElementsByTagName('td'),j=0,tdlength=tds.length; j<tdlength; j++) {
							td = tds[j];
							copyChildElm(p, td);
						}
						obj.parentNode.insertBefore(p.cloneNode(1), obj);
						p = undefined;
					}
					obj.parentNode.removeChild(obj);
					return newObj
				}

				// 清除标签最前端的空白
				var trimFront = function(obj) {
					var textNode = obj.childNodes[0];
					if (typeof (textNode) == 'undefined' || typeof (textNode.nodeType) == 'undefined') {
						return
					}

					if (textNode.nodeType == 3) {
						// IE
						if(document.all){
							obj.innerHTML = obj.innerHTML.replace(/^(\s|&nbsp;|　|\u3000)*/, '');
							return;
						}
						textNode.textContent = textNode.textContent.replace(/^(\s|&nbsp;|　|\u3000)*/, '');
					}
				}

				// 一键排版 start
				if (ed.selection.isCollapsed()) {
					// 虽然不知道为什么但是不加这个会出问题
					ed.getContent();
					// 无元素选中时匹配全dom
					htmlDom = ed.getBody().children;
				} else {
					htmlDom = ed.selection.getSelectedBlocks();
				}
				if (typeof(htmlDom) != 'object') {
					return;
				}
				for (var pItem,oItem,domIndex=0,domLength = htmlDom.length;typeof(htmlDom[domIndex]) != 'undefined'; domIndex++) {
					oItem = htmlDom[domIndex];
					pItem = document.createElement('div');
					pItem.setAttribute('data-mce-type','temp_container');
					if (typeof(oItem)=='undefined') {
						continue;
					}					
					pItem.appendChild(oItem.cloneNode(1));
					// 格式化结点
					parseElement(pItem.childNodes[0]);
					
					// 转换结果为空结点时
					if (pItem.childNodes.length == 0) {
						oItem.parentNode.removeChild(oItem);
						continue;
					}
					// 格式化结果为文本结点时添加p标签;
					if (pItem.childNodes[0].nodeType == 3) {
						pItem.innerHTML = '<p>' + pItem.innerHTML + '</p>';
					}
					
					for (var item,l=pItem.childNodes.length,i=0; i<l; i++) {
						item = pItem.cloneNode(1).childNodes[i];
						if (!item) {
							continue;
						}
						// 处理空白p标签
						if (typeof(item.tagName) != 'undefined' && item.tagName.toLocaleLowerCase() == 'p') {
							item = fuckP(item);
							try {
								if (!item || (item.getAttribute('data-mce-type') == 'temp_container')) {
									domIndex--;
									continue;
								}
							} catch (exc) {}
						}
						if (item.nodeType == 1 && item.getAttribute('data-mce-type') == 'temp_container') {
							continue;
						}
						oItem.parentNode.insertBefore(item.cloneNode(1), oItem);
						item = undefined;
					}
					if (oItem) oItem.parentNode.removeChild(oItem);
					pItem = undefined;
					// fix: chrome下一键排版后视频会丢失显示
					ed.getContent();
				}
			});

			//注册按钮
			ed.addButton('onekeyclear', {
				title : '\u4e00\u952e\u6392\u7248',
				cmd : 'mceOneKeyClear'
			});

			// 覆盖对齐方法添加data-mce-style
			ed.addCommand('JustifyLeft', function() {
				var nodes = ed.selection.isCollapsed() ? [ed.selection.getNode()] : ed.selection.getSelectedBlocks();
				var matches = tinymce.map(nodes, function(node) {
					var tempElm = document.createElement('span');
					tempElm.setAttribute('style', node.getAttribute('data-mce-style'));
					tempElm.style.textAlign = 'left';
					node.setAttribute('data-mce-style', tempElm.getAttribute('style'));
					node.style.textAlign = 'left';
					tempElm = undefined;
				});
				return tinymce.inArray(matches, true) !== -1;
			});
			ed.addCommand('JustifyCenter', function() {
				var nodes = ed.selection.isCollapsed() ? [ed.selection.getNode()] : ed.selection.getSelectedBlocks();
				var matches = tinymce.map(nodes, function(node) {
					var tempElm = document.createElement('span');
					tempElm.setAttribute('style', node.getAttribute('data-mce-style'));
					tempElm.style.textAlign = 'center';
					node.setAttribute('data-mce-style', tempElm.getAttribute('style'));
					node.style.textAlign = 'center';
					tempElm = undefined;
				});
				return tinymce.inArray(matches, true) !== -1;
			});
			ed.addCommand('JustifyRight', function() {
				var nodes = ed.selection.isCollapsed() ? [ed.selection.getNode()] : ed.selection.getSelectedBlocks();
				var matches = tinymce.map(nodes, function(node) {
					var tempElm = document.createElement('span');
					tempElm.setAttribute('style', node.getAttribute('data-mce-style'));
					tempElm.style.textAlign = 'right';
					node.setAttribute('data-mce-style', tempElm.getAttribute('style'));
					node.style.textAlign = 'right';
					tempElm = undefined;
				});
				return tinymce.inArray(matches, true) !== -1;
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('onekeyclear', tinymce.plugins.OneKeyClearPlugin);
})();

// IE的String对象无trim()函数的解决方案
if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, ''); 
  }
}