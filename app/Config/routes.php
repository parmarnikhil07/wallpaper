<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
Configure::write('Routing.prefixes', array('api'));

/***************************************API**********************************************/
Router::connect('/', array('controller' => 'dashboards', 'action' => 'index'));

Router::connect('/authorize/get_access_key/:publicKey', array('controller' => 'authorize', 'action' => 'get_access_key', 'type' => 'api'), array('api' => 'api', 'pass' => array('publicKey')));

Router::connect('/users/get_slider_images/:accesskey', array('controller' => 'users', 'action' => 'get_slider_images', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/register/:accesskey', array('controller' => 'users', 'action' => 'register', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/login/:accesskey', array('controller' => 'users', 'action' => 'login', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/forgot_password/:accesskey', array('controller' => 'users', 'action' => 'forgot_password', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/logout/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'logout', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'device_unique_id')));
Router::connect('/users/get_country_list/:accesskey', array('controller' => 'users', 'action' => 'get_country_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/check_avail_user_name/:accesskey/:user_name', array('controller' => 'users', 'action' => 'check_avail_user_name', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'user_name')));
Router::connect('/users/get_user_detail/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'get_user_detail', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id')));
Router::connect('/users/user_name_suggest/:accesskey', array('controller' => 'users', 'action' => 'user_name_suggest', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/set_user_detail/:accesskey/:userkey', array('controller' => 'users', 'action' => 'set_user_detail', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/change_password/:accesskey/:userkey', array('controller' => 'users', 'action' => 'change_password', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/set_user_name/:accesskey/:userkey', array('controller' => 'users', 'action' => 'set_user_name', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/get_followers/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'get_followers', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/users/get_following/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'get_following', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/users/search_following/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'search_following', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/users/search_followers/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'search_followers', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/users/set_follow_unfollow/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'set_follow_unfollow', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'following_id')));
Router::connect('/users/update_app_version/:accesskey/*', array('controller' => 'users', 'action' => 'update_app_version', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/get_all_user_list/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'get_all_user_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'page')));
Router::connect('/users/search_all_user_list/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'search_all_user_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'page')));
Router::connect('/users/global_setting/:accesskey/:userkey', array('controller' => 'users', 'action' => 'global_setting', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/get_activity_feed/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'get_activity_feed', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/users/get_news_detail/:accesskey/:userkey/:id', array('controller' => 'users', 'action' => 'get_news_detail', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'id')));

Router::connect('/wallpapers/create_soapbox/:accesskey/:userkey', array('controller' => 'wallpapers', 'action' => 'create_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/wallpapers/get_soapbox_detail/:accesskey/:userkey/*', array('controller' => 'wallpapers', 'action' => 'get_soapbox_detail', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'soapbox_id')));
Router::connect('/wallpapers/get_my_soapbox_list/:accesskey/:userkey/*', array('controller' => 'wallpapers', 'action' => 'get_my_soapbox_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'page')));
Router::connect('/wallpapers/today_past_soapbox_list/:accesskey/:userkey/*', array('controller' => 'wallpapers', 'action' => 'today_past_soapbox_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/wallpapers/todays_new_wallpapers_list/:accesskey/:userkey/*', array('controller' => 'wallpapers', 'action' => 'todays_new_wallpapers_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/wallpapers/like_soapbox/:accesskey/:userkey', array('controller' => 'wallpapers', 'action' => 'like_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/wallpapers/rating_soapbox/:accesskey/:userkey', array('controller' => 'wallpapers', 'action' => 'rating_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/wallpapers/trending_interest_list/:accesskey/:userkey', array('controller' => 'wallpapers', 'action' => 'trending_interest_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/wallpapers/trending_interest_soapbox_list/:accesskey/:userkey/*', array('controller' => 'wallpapers', 'action' => 'trending_interest_soapbox_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'interest_id', 'page')));
Router::connect('/wallpapers/delete_soapbox/:accesskey/:userkey/*', array('controller' => 'wallpapers', 'action' => 'delete_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'soapbox_id')));
Router::connect('/wallpapers/search_soapbox/:accesskey/:userkey', array('controller' => 'wallpapers', 'action' => 'search_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/wallpapers/edit_soapbox/:accesskey/:userkey', array('controller' => 'wallpapers', 'action' => 'edit_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/wallpapers/get_custom_invited_data/:accesskey/:userkey/:soapbox_id', array('controller' => 'wallpapers', 'action' => 'get_custom_invited_data', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'soapbox_id')));

Router::connect('/comments/comment_soapbox/:accesskey/:userkey', array('controller' => 'comments', 'action' => 'comment_soapbox', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/comments/get_comments/:accesskey/:userkey/*', array('controller' => 'comments', 'action' => 'get_comments', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'soapbox_id')));

Router::connect('/groups/get_group_list/:accesskey/:userkey', array('controller' => 'groups', 'action' => 'get_group_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/groups/get_group_users/:accesskey/:userkey/*', array('controller' => 'groups', 'action' => 'get_group_users', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'group_id')));
Router::connect('/groups/create_group/:accesskey/:userkey', array('controller' => 'groups', 'action' => 'create_group', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/groups/delete_group/:accesskey/:userkey/*', array('controller' => 'groups', 'action' => 'delete_group', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'group_id')));
Router::connect('/groups/delete_group_users/:accesskey/:userkey', array('controller' => 'groups', 'action' => 'delete_group_users', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/groups/set_group_users/:accesskey/:userkey', array('controller' => 'groups', 'action' => 'set_group_users', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));

Router::connect('/contacts/get_messages/:accesskey/:lang', array('controller' => 'contacts', 'action' => 'get_messages', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'lang')));
Router::connect('/contacts/add_contact_manually/:accesskey/:userkey', array('controller' => 'contacts', 'action' => 'add_contact_manually', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/contacts/add_contact_by_user_id/:accesskey/:userkey/*', array('controller' => 'contacts', 'action' => 'add_contact_by_user_id', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id')));
Router::connect('/contacts/get_contacts/:accesskey/:userkey/*', array('controller' => 'contacts', 'action' => 'get_contacts', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'contactType', 'page')));
Router::connect('/contacts/respond_to_contact_request/:accesskey/:userkey', array('controller' => 'contacts', 'action' => 'respond_to_contact_request', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/contacts/request_as_friend/:accesskey/:userkey', array('controller' => 'contacts', 'action' => 'request_as_friend', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/contacts/delete_contact/:accesskey/:userkey/*', array('controller' => 'contacts', 'action' => 'delete_contact', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'contact_table_id')));
Router::connect('/contacts/remove_from_contact_to_network/:accesskey/:userkey/*', array('controller' => 'contacts', 'action' => 'remove_from_contact_to_network', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'contact_table_id')));
Router::connect('/contacts/suggest_contact/:accesskey/:userkey/*', array('controller' => 'contacts', 'action' => 'suggest_contact', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'page')));
Router::connect('/contacts/get_friend_request_list/:accesskey/:userkey/*', array('controller' => 'contacts', 'action' => 'get_friend_request_list', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'page')));
Router::connect('/contacts/sync_contact/:accesskey/:userkey', array('controller' => 'contacts', 'action' => 'sync_contact', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));

Router::connect('/interests/get_all_interests/:accesskey/:userkey', array('controller' => 'interests', 'action' => 'get_all_interests', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/interests/get_all_interest_wallpapers/:accesskey/:userkey/*', array('controller' => 'interests', 'action' => 'get_all_interest_wallpapers', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'interest_id', 'page')));

Router::connect('/spam_reports/spam_data/:accesskey/:userkey', array('controller' => 'spam_reports', 'action' => 'spam_data', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));


Router::connect('/friend_request/:code', array('controller' => 'users', 'action' => 'friend_request_schema', 'type' => 'api'), array('api' => 'api', 'pass' => array('code')));
Router::connect('/soapbox_invite/:code', array('controller' => 'wallpapers', 'action' => 'soapbox_invite_schema', 'type' => 'api'), array('api' => 'api', 'pass' => array('code')));




Router::connect('/users/validateAccount/:code', array('controller' => 'users', 'action' => 'validateAccount', 'type' => 'api'), array('api' => 'api', 'pass' => array('code')));
Router::connect('/users/block/:code', array('controller' => 'users', 'action' => 'block', 'type' => 'api'), array('api' => 'api', 'pass' => array('code')));
Router::connect('/users/sendVerificationMail/:accesskey/:userkey', array('controller' => 'users', 'action' => 'sendVerificationMail', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/insertContact/:accesskey/:userkey', array('controller' => 'users', 'action' => 'insertContact', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/insertContactById/:accesskey/:userkey', array('controller' => 'users', 'action' => 'insertContactById', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/deleteContact/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'deleteContact', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'id')));
Router::connect('/users/removeFromFriendToNetwork/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'removeFromFriendToNetwork', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'id')));
Router::connect('/users/removeFromFriend/:accesskey/:userkey', array('controller' => 'users', 'action' => 'removeFromFriend', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/requestAsFriend/:accesskey/:userkey', array('controller' => 'users', 'action' => 'requestAsFriend', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/appBlockUser/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'appBlockUser', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'id')));
Router::connect('/users/appUnBlockUser/:accesskey/:userkey/*', array('controller' => 'users', 'action' => 'appUnBlockUser', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'id')));
Router::connect('/users/getContactRequestList/:accesskey/:userkey', array('controller' => 'users', 'action' => 'getContactRequestList', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/responseToContactRequest/:accesskey/:userkey', array('controller' => 'users', 'action' => 'responseToContactRequest', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey')));
Router::connect('/users/getUserActivityFeed/:accesskey/:userkey/:user_id/:page', array('controller' => 'users', 'action' => 'getUserActivityFeed', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey', 'userkey', 'user_id', 'page')));
Router::connect('/users/getInterestList/:accesskey', array('controller' => 'users', 'action' => 'getInterestList', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/send_test_notificaiton/:accesskey', array('controller' => 'users', 'action' => 'send_test_notificaiton', 'type' => 'api'), array('api' => 'api', 'pass' => array('accesskey')));
Router::connect('/users/forgotPasswordReset/:code', array('controller' => 'users', 'action' => 'forgotPasswordReset', 'type' => 'api'), array('api' => 'api', 'pass' => array('code')));

/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';