<?php
class queue_mail extends queue_engine
{
    function execute(array $params)
    {
        $mail_setting = (array) setting('system', 'mail');

        $from = value($mail_setting, 'from');
        $to = value($params, 'to');
        $subject = value($params, 'subject');

        if (!$to)
        {
            $this->_error = 'need param to';
            return false;
        }

        if (!$subject)
        {
            $this->_error = 'need param subject';
            return false;
        }

        if (empty($params['content']))
        {
            $this->_error = 'need param content';
            return false;
        }

        $mailer = & factory::sendmail();
        $result = $mailer->execute($to, $subject, $params['content'], $from);
        if (!$result) $this->_error = $mailer->error();
        return $result;
    }
}