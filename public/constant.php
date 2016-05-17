<?php

/*
|--------------------------------------------------------------------------
| Variable defined in Apache Server configuration
|--------------------------------------------------------------------------
*/
define ("SERVER_BETA_KEY",'INTRANET_DEMO');


/*
|--------------------------------------------------------------------------
| for Application purpose -- not for settings
|--------------------------------------------------------------------------
*/

define ("LOCAL",'local');
define ("BETA",'beta');
define ("PRODUCTION",'production');

/*
|--------------------------------------------------------------------------
| User Roles
|--------------------------------------------------------------------------
*/


define ("WEB_MASTER",1000);
define ("INSTRUCTOR",500);
define ("TEACHING_ASSISTANT",200);
define ("STUDENT",100);
define ("GUEST",10);

/*
|--------------------------------------------------------------------------
| stats tracking User/Permission Roles
|--------------------------------------------------------------------------
*/

define ("ADMIN", "admin");
define ("REGULAR", "regular");
define ("PERMISSION", "permission_level");

/*
|--------------------------------------------------------------------------
| VIEW STRING
|--------------------------------------------------------------------------
*/

define ("STR_WEB_MASTER","WebMaster");
define ("STR_INSTRUCTOR","Instructor");
define ("STR_TEACHING_ASSISTANT","Teaching Assistant");
define ("STR_STUDENT","Student");
define ("STR_GUEST","Guest");



/*
|--------------------------------------------------------------------------
| INTRANET and WEB_SERVICE URL 
|--------------------------------------------------------------------------
*/

define ("WEBSRV_URL","https://websrv.utsc.utoronto.ca:8443/axis2/services/PHPService");
define ("WEBSRV_BETA_URL","https://websrv-beta.utsc.utoronto.ca:9443/axis2/services/PHPService");
define ("INTRANET","https://intranet.utsc.utoronto.ca/home.php");
define ("INTRANET_BETA","https://intranet-beta.utsc.utoronto.ca/home.php");

define ("SESSION_TIMEOUT",440);


/*
|--------------------------------------------------------------------------
| STATUS Messages
|--------------------------------------------------------------------------
*/

define ("STATUS_ANSWER_DRAFT",0);
define ("STATUS_ANSWER_SUBMIT",1);
define ("STATUS_ANSWER_REJECT",440);




define ("STATUS_COURSE_INACTIVE",-1);
define ("STATUS_COURSE_PENDING",0);
define ("STATUS_COURSE_ACTIVE",1);


define ("STATUS_ALERT_SUCCESS",1);
define ("STATUS_ALERT_WARNING",-1);
define ("STATUS_ALERT_DANGER",-10);


define ("MSG_STATUS_ALERT_MESSAGE_NOT_YET_CONFIGURED","Your lecture schedule has not been configured. Please configure before you activate your course");
define ("MSG_STATUS_ALERT_MESSAGE_NOT_YET_ACTIVATED","Your lecture section has not been activated.");





