SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_survey`;
DROP TABLE IF EXISTS `cmstop_survey_answer`;
DROP TABLE IF EXISTS `cmstop_survey_answer_option`;
DROP TABLE IF EXISTS `cmstop_survey_answer_record`;
DROP TABLE IF EXISTS `cmstop_survey_question`;
DROP TABLE IF EXISTS `cmstop_survey_question_option`;


DELETE FROM `cmstop_aca` WHERE `app`='survey';
DELETE FROM `cmstop_model` WHERE `modelid`='9';