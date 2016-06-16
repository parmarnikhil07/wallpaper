<?php

App::uses('AppController', 'Controller');

/**
 * Users controller
 *
 * @package    URateIt
 * @subpackage eventzBuddyApi.Controllers
 */
class SpamReportsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     *
     */
    public $name = 'SpamReports';
    /**
     * Models name
     * @var array
     */
    public $uses = array('SpamReport', 'User', 'WallpaperComment', 'Wallpaper', 'WallpaperRating', 'WallpaperCustomInvite', 'WallpaperLike', 'ActivityFeed', 'UserDevice', 'Contact', 'Group', 'GroupUser', 'UserFollower', 'Template');

    /**
     * report from app 
     */
    public function spam_data() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $validationErrors = false;
            if (empty($this->request->data['sender_id'])) {
                $this->apiErrors[] = 'sender_id is missing.';
                $validationErrors = true;
            }
            if (empty($this->request->data['report_type'])) {
                $this->apiErrors[] = 'type is missing.';
                $validationErrors = true;
            }
            if (empty($this->request->data['reason_title'])) {
                $this->apiErrors[] = 'type is missing.';
                $validationErrors = true;
            }
            $componentLine = "";
            if (!$validationErrors) {
                if ($this->request->data['report_type'] == 'user') {
                    if (!empty($this->request->data['user_id'])) {
                        $this->request->data['component_id'] = $this->request->data['user_id'];
                        $userArr = $this->User->find('first', array('conditions' => array('User.id' => $this->request->data['user_id'])));
                        if (empty($userArr)) {
                            $this->errorsToReturn[] = 'User is deleted/not found.';
                            $validationErrors = true;
                        } else {
                            $componentLine = "<b> Reported User: </b> <span>" . $userArr['User']['full_name'] . " (" . $userArr['User']['user_name'] . ") " . "</span> <br/><br/>";
                        }
                    } else {
                        $this->apiErrors[] = 'user_id is missing.';
                        $validationErrors = true;
                    }
                } else if ($this->request->data['report_type'] == 'wallpaper') {
                    if (!empty($this->request->data['wallpaper_id'])) {
                        $this->request->data['component_id'] = $this->request->data['wallpaper_id'];
                        $wallpaperArr = $this->Wallpaper->find('first', array('conditions' => array("Wallpaper.id" => $this->request->data['wallpaper_id'])));
                        if (empty($wallpaperArr)) {
                            $this->errorsToReturn[] = 'Wallpaper is deleted/not found.';
                            $validationErrors = true;
                        } else {
                            $componentLine = "<b> Reported Wallpaper: </b> <span>" . '<a href="' . WEB_URL . 'wallpapers/show/' . $this->encrypt($wallpaperArr['Wallpaper']['id'], ENCRYPT_DECRYPT_KEY) . ' "style ="color:rgb(238,110,23)" >' . $wallpaperArr['Wallpaper']['title'] . '</a>' . "</span> <br/><br/>";
                        }
                    } else {
                        $this->apiErrors[] = 'wallpaper_id is missing.';
                        $validationErrors = true;
                    }
                } else if ($this->request->data['report_type'] == 'comment') {
                    if (!empty($this->request->data['comment_id'])) {
                        $this->request->data['component_id'] = $this->request->data['comment_id'];
                        $commentArr = $this->WallpaperComment->find('first', array('conditions' => array('WallpaperComment.id' => $this->request->data['comment_id'])));
                        if (empty($commentArr)) {
                            $this->errorsToReturn[] = 'Comment is deleted/not found.';
                            $validationErrors = true;
                        } else {
                            $componentLine = "<b> Reported Comment: </b> <span>" . $commentArr['WallpaperComment']['comment'] . "</span> <br/><br/>";
                        }
                    } else {
                        $this->apiErrors[] = 'comment_id is missing.';
                        $validationErrors = true;
                    }
                } else {
                    $this->apiErrors[] = 'wrong type.';
                    $validationErrors = true;
                }
            }
            if (!$validationErrors) {
                $result = $this->SpamReport->save($this->request->data);
                $senderArr = $this->User->find('first', array('conditions' => array('User.id' => $this->request->data['sender_id'])));
                if ($result) {
//                    $emailData = '<div style="width:300px;text-align:left;padding-left:20px;"> <br/><br/>';
//                    $emailData .= '<b> Sender : </b> <span>' . $senderArr['User']['full_name'] . '</span> <br/><br/>';
//                    $emailData .= $componentLine;
//                    $emailData .= '<b> Reason: </b> <span>' . $this->request->data['reason_title'] . '</span> <br/><br/>';
//                    $emailData .= '<b> Description: </b> <span>' . $this->request->data['description'] . '</span> <br/><br/>';
//                    $emailData .= '</div>';
//                    if (EMAIL_SETTING != "mendrillAPI") {
//                        $this->send_email('Report From App', $emailData, 'nikhil.parmar@multidots.in', 'mail_temp');
//                    } else {
//                        $emailData = $this->addHdrFtr($emailData);
//                        $this->mandrillEmailToArr[] = array("email" => 'nikhil.parmar@multidots.in', "name" => "Nikhil Parmar", "type" => 'to');
//                        $varArr = array("name" => "EMAIL", "content" => $emailData);
//                        $this->mandrillEmailVarsArr[] = array("rcpt" => 'gautam@multidots.in', "vars" => array($varArr));
//                        $this->mandrill_api_email('Report From App', $this->mandrillEmailToArr, $this->mandrillEmailVarsArr);
//                        $this->mandrillEmailVarsArr = null;
//                        $this->mandrillEmailToArr = null;
//                    }
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_FAIL;
                }
            } else {
                $this->apiResponseCode = API_CODE_FAIL;
            }
        } else {
            $this->apiResponseCode = API_CODE_NO_POST;
        }
    }

    /* list for comment reports
     * 
     */

    public function report_comments() {
        try {
            $this->paginate = array(
                'conditions' => array('SpamReport.report_type' => 'comment', 'SpamReport.is_attended' => 0),
                'limit' => PAGE_LIMIT,
                'fields' => array('User.user_name', 'SpamReport.id', 'SpamReport.reason_title', 'WallpaperComment.wallpaper_id', 'WallpaperComment.user_id', 'WallpaperComment.comment', 'Wallpaper.title', 'Wallpaper.video', 'CommentUser.user_name'),
                'order' => array('created' => 'desc'),
                'joins' => array(
                    array(
                        'table' => 'wallpaper_comments',
                        'type' => 'INNER',
                        'alias' => 'WallpaperComment',
                        'conditions' => array('WallpaperComment.id = SpamReport.component_id')
                    ),
                    array(
                        'table' => 'users',
                        'type' => 'LEFT',
                        'alias' => 'User',
                        'conditions' => array('User.id = SpamReport.sender_id')
                    ),
                    array(
                        'table' => 'wallpapers',
                        'type' => 'LEFT',
                        'alias' => 'Wallpaper',
                        'conditions' => array('Wallpaper.id = WallpaperComment.wallpaper_id')
                    ),
                    array(
                        'table' => 'users',
                        'type' => 'LEFT',
                        'alias' => 'CommentUser',
                        'conditions' => array('CommentUser.id = WallpaperComment.user_id')
                    )
                )
            );
            $this->SpamReport->recursive = -1;
            $this->set('spamsArr', $this->paginate('SpamReport'));
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/spam_reports/report_comments");
        }
        $this->set('title_for_layout', 'Comments reports');
    }

    /*
     * delete comment from db
     */

    public function delete_comment_report($spam_id = null) {
        $spamData = $this->SpamReport->find('first', array('conditions' => array('SpamReport.id' => $spam_id)));
        if (!empty($spamData)) {
            $spamData['SpamReport']['is_attended'] = true;
            $this->SpamReport->save($spamData);
            $userArr = array();
            $commentArr = array();
            if (!empty($spamData['SpamReport']['component_id'])) {
                $commentArr = $this->WallpaperComment->find("first", array('conditions' => array('WallpaperComment.id' => $spamData['SpamReport']['component_id'])));
                if (!empty($commentArr)) {
                    $userArr = $this->User->find('first', array('conditions' => array('User.id' => $commentArr['WallpaperComment']['user_id'])));
                }
                if (!empty($userArr) && !empty($commentArr)) {
                    $this->WallpaperComment->updateAll(array('WallpaperComment.is_deleted' => 1), array('WallpaperComment.id' => $spamData['SpamReport']['component_id']));
                    $this->ActivityFeed->updateAll(array('ActivityFeed.is_deleted' => 1), array('ActivityFeed.wallpaper_id' => $commentArr['WallpaperComment']['wallpaper_id'], 'ActivityFeed.feed_user_id' => $commentArr['WallpaperComment']['user_id'], 'ActivityFeed.action Like' => '%comment_wallpaper%'));
//                    $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'delete_comment_report'),
//                        'fields' => array('subject', 'content', 'tags')));
//                    $arr1 = explode(',', $templateDetail['Template']['tags']);
//                    $arr2 = array($userArr['User']['full_name'], $commentArr['WallpaperComment']['comment']);
//                    $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
//                    try {
//                        if (strpos($userArr['User']['email'], 'example') == false) {
//                            if (EMAIL_SETTING != "mendrillAPI") {
//                                $this->send_email($templateDetail['Template']['subject'], $emailData, $userArr['User']['email'], 'mail_temp');
//                            } else {
//                                $emailData = $this->addHdrFtr($emailData);
//                                $this->mandrillEmailToArr[] = array("email" => $userArr['User']['email'], "name" => $userArr['User']['full_name'], "type" => 'to');
//                                $varArr = array("name" => "EMAIL", "content" => $emailData);
//                                $this->mandrillEmailVarsArr[] = array("rcpt" => $userArr['User']['email'], "vars" => array($varArr));
//                                $this->mandrill_api_email($templateDetail['Template']['subject'], $this->mandrillEmailToArr, $this->mandrillEmailVarsArr);
//                                $this->mandrillEmailVarsArr = null;
//                                $this->mandrillEmailToArr = null;
//                            }
//                        }
//                    } catch (Exception $e) {
//                        
//                    }
                    $this->Session->setFlash('Comment was successfully deleted.', 'flashSuccess');
                } else {
                    $this->Session->setFlash('Comment was already deleted.', 'flashError');
                }
                $this->redirect("/spam_reports/report_comments");
            } else {
                $this->Session->setFlash('Comment not found.', 'flashError');
                $this->redirect("/spam_reports/report_comments");
            }
        } else {
            $this->Session->setFlash('Report not found.', 'flashError');
            $this->redirect("/spam_reports/report_comments");
        }
    }

    /* list for comment wallpapers
     * 
     */

    public function report_wallpapers() {
        try {
            $this->paginate = array(
                'conditions' => array('SpamReport.report_type' => 'wallpaper', 'SpamReport.is_attended' => 0),
                'limit' => PAGE_LIMIT,
                'recursive' => 1,
                'fields' => array('User.user_name', 'SpamReport.id', 'SpamReport.reason_title', 'Wallpaper.id', 'Wallpaper.video', 'Wallpaper.title', 'WallpaperUser.user_name'),
                'order' => array('created' => 'desc'),
                'joins' => array(
                    array(
                        'table' => 'wallpapers',
                        'type' => 'LEFT',
                        'alias' => 'Wallpaper',
                        'conditions' => array('Wallpaper.id = SpamReport.component_id')
                    ),
                    array(
                        'table' => 'users',
                        'type' => 'LEFT',
                        'alias' => 'WallpaperUser',
                        'conditions' => array('Wallpaper.user_id = WallpaperUser.id')
                    ),
                    array(
                        'table' => 'users',
                        'type' => 'LEFT',
                        'alias' => 'User',
                        'conditions' => array('User.id = SpamReport.sender_id')
                    )
                )
            );

            $this->set('spamsArr', $this->paginate('SpamReport'));
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/spam_reports/report_comments");
        }
        $this->set('title_for_layout', 'Wallpapers reports');
    }

    public function delete_wallpaper_report($spam_id = null) {
        if (!empty($spam_id)) {
            $spamData = $this->SpamReport->find('first', array('conditions' => array('SpamReport.id' => $spam_id)));
            if (!empty($spamData)) {
                $spamData['SpamReport']['is_attended'] = true;
                $this->SpamReport->save($spamData);
                $userArr = array();
                $wallpaperArr = $this->Wallpaper->find("first", array('conditions' => array('Wallpaper.id' => $spamData['SpamReport']['component_id']), 'fields' => array('Wallpaper.id')));
                if (!empty($wallpaperArr['Wallpaper']['id'])) {
                    $this->Wallpaper->updateAll(array('Wallpaper.is_deleted' => 1), array('Wallpaper.id' => $wallpaperArr['Wallpaper']['id']));
                    
                    $this->WallpaperRating->updateAll(array('WallpaperRating.is_deleted' => 1), array('WallpaperRating.wallpaper_id' => $wallpaperArr['Wallpaper']['id']));

                    /* Delete Wallpaper Comments */
                    $this->WallpaperComment->updateAll(array('WallpaperComment.is_deleted' => 1), array('WallpaperComment.wallpaper_id' => $wallpaperArr['Wallpaper']['id']));

                    /* Delete Wallpaper Comments */
                    $this->WallpaperLike->updateAll(array('WallpaperLike.is_deleted' => 1), array('WallpaperLike.wallpaper_id' => $wallpaperArr['Wallpaper']['id']));

                    /* Delete Wallpaper custom invites */
                    $this->WallpaperCustomInvite->updateAll(array('WallpaperCustomInvite.is_deleted' => 1), array('WallpaperCustomInvite.wallpaper_id' => $wallpaperArr['Wallpaper']['id']));

                    /* Delete User feeds */
                    $this->ActivityFeed->updateAll(array('ActivityFeed.is_deleted' => 1), array('ActivityFeed.wallpaper_id' => $wallpaperArr['Wallpaper']['id'], 'ActivityFeed.action Like' => '%wallpaper%'));
                    $this->Session->setFlash('Wallpaper successfully deleted.', 'flashSuccess');
                } else {
                    $this->Session->setFlash('Wallpaper was already deleted.', 'flashError');
                }
                $this->redirect("/spam_reports/report_wallpapers");
            } else {
                $this->Session->setFlash('Report not found.', 'flashError');
                $this->redirect("/spam_reports/report_wallpapers");
            }
        } else {
            $this->Session->setFlash('No data posted', 'flashError');
            $this->redirect("/spam_reports/report_wallpapers");
        }
    }

    public function wallpaperDeleteQueries($wallpaper_id = null) {
        if (!empty($wallpaper_id)) {
            $wallpaperArr = $this->Wallpaper->find("first", array('conditions' => array('Wallpaper.id' => $wallpaper_id)));
            if (!empty($wallpaperArr)) {
                /* Delete Wallpaper Answers */
                $this->WallpaperRating->updateAll(array('WallpaperRating.is_deleted' => 1), array('WallpaperRating.wallpaper_id' => $wallpaperArr));

                /* Delete Wallpaper Comments */
                $this->WallpaperComment->updateAll(array('WallpaperComment.is_deleted' => 1), array('WallpaperComment.wallpaper_id' => $wallpaperArr));

                /* Delete Wallpaper Comments */
                $this->WallpaperLike->updateAll(array('WallpaperLike.is_deleted' => 1), array('WallpaperLike.wallpaper_id' => $wallpaperArr));

                /* Delete Wallpaper Comments */
                $this->WallpaperCustomInvite->updateAll(array('WallpaperCustomInvite.is_deleted' => 1), array('WallpaperCustomInvite.wallpaper_id' => $wallpaperArr));

                /* Delete User feeds */
                $this->ActivityFeed->updateAll(array('ActivityFeed.is_deleted' => 1), array('ActivityFeed.wallpaper_id' => $wallpaperArr, 'ActivityFeed.action Like' => '%wallpaper%'));
                $userArr = array();
                if (!empty($wallpaperArr)) {
                    $userArr = $this->User->find('first', array('conditions' => array('User.id' => $wallpaperArr['Wallpaper']['user_id'])));
                }
                if (!empty($userArr)) {
//                    $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'delete_wallpaper_report'),
//                        'fields' => array('subject', 'content', 'tags')));
//                    $arr1 = explode(',', $templateDetail['Template']['tags']);
//                    $arr2 = array($userArr['User']['full_name'], $wallpaperArr['Wallpaper']['title']);
//                    $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
//                    try {
//                        if (strpos($userArr['User']['email'], 'example') == false) {
//                            if (EMAIL_SETTING != "mendrillAPI") {
//                                $this->send_email($templateDetail['Template']['subject'], $emailData, $userArr['User']['email'], 'mail_temp');
//                            } else {
//                                $emailData = $this->addHdrFtr($emailData);
//                                $this->mandrillEmailToArr[] = array("email" => $userArr['User']['email'], "name" => $userArr['User']['full_name'], "type" => 'to');
//                                $varArr = array("name" => "EMAIL", "content" => $emailData);
//                                $this->mandrillEmailVarsArr[] = array("rcpt" => $userArr['User']['email'], "vars" => array($varArr));
//                                $this->mandrill_api_email($templateDetail['Template']['subject'], $this->mandrillEmailToArr, $this->mandrillEmailVarsArr);
//                                $this->mandrillEmailVarsArr = null;
//                                $this->mandrillEmailToArr = null;
//                            }
//                        }
//                    } catch (Exception $e) {
//                        
//                    }
                }
            }
        }
    }

    /* list for comment wallpapers
     * 
     */

    public function report_users() {
        try {
            $this->paginate = array(
                'conditions' => array('SpamReport.report_type' => 'user', 'SpamReport.is_attended' => 0),
                'limit' => PAGE_LIMIT,
                'recursive' => 1,
                'fields' => array('SenderUser.user_name', 'SpamReport.id', 'SpamReport.reason_title', 'User.user_name', 'User.email', 'User.created'),
                'order' => array('created' => 'desc'),
                'joins' => array(
                    array(
                        'table' => 'users',
                        'type' => 'LEFT',
                        'alias' => 'User',
                        'conditions' => array('User.id = SpamReport.component_id')
                    ),
                    array(
                        'table' => 'users',
                        'type' => 'LEFT',
                        'alias' => 'SenderUser',
                        'conditions' => array('SenderUser.id = SpamReport.sender_id')
                    )
                )
            );

            $this->set('spamsArr', $this->paginate('SpamReport'));
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/spam_reports/report_comments");
        }
        $this->set('title_for_layout', 'User reports');
    }

    public function delete_user_report($spam_id = null) {
        if (!empty($spam_id)) {
            $spamData = $this->SpamReport->find('first', array('conditions' => array('SpamReport.id' => $spam_id)));
            if (!empty($spamData)) {
                $spamData['SpamReport']['is_attended'] = true;
                $this->SpamReport->save($spamData);
                $userArr = $this->User->find('first', array('conditions' => array('User.id' => $spamData['SpamReport']['component_id'])));
                if (!empty($userArr)) {
                    $this->User->updateAll(array('User.is_deleted' => 1), array('User.id' => $spamData['SpamReport']['component_id']));

                    $this->Contact->updateAll(array('Contact.is_deleted' => 1), array('Contact.to_user_id' => $spamData['SpamReport']['component_id']));
                    $this->Contact->updateAll(array('Contact.is_deleted' => 1), array('Contact.from_user_id' => $spamData['SpamReport']['component_id']));

                    $this->UserDevice->updateAll(array('UserDevice.is_deleted' => 1), array('UserDevice.user_id' => $spamData['SpamReport']['component_id']));

                    $this->UserFollower->updateAll(array('UserFollower.is_deleted' => 1), array('UserFollower.follower_id' => $spamData['SpamReport']['component_id']));
                    $this->UserFollower->updateAll(array('UserFollower.is_deleted' => 1), array('UserFollower.following_id' => $spamData['SpamReport']['component_id']));

                    $groupIdArr = $this->Group->find('list', array('conditions' => array('Group.user_id' => $spamData['SpamReport']['component_id']), 'fields' => array('Group.id')));
                    if (!empty($groupIdArr)) {
                        $this->Group->updateAll(array('Group.is_deleted' => 1), array('Group.user_id' => $spamData['SpamReport']['component_id']));
                        $this->GroupUser->updateAll(array('GroupUser.is_deleted' => 1), array('GroupUser.group_id' => $groupIdArr));
                    }
                    $this->GroupUser->updateAll(array('GroupUser.is_deleted' => 1), array('GroupUser.user_id' => $spamData['SpamReport']['component_id']));

                    $wallpaperArr = $this->Wallpaper->find('list', array("conditions" => array("Wallpaper.user_id" => $spamData['SpamReport']['component_id']), 'fields' => array('Wallpaper.id')));
                    $this->Wallpaper->updateAll(array('Wallpaper.is_deleted' => 1), array('Wallpaper.user_id' => $spamData['SpamReport']['component_id']));
                    if (!empty($wallpaperArr)) {

                        /* Delete Wallpaper Answers */
                        $this->WallpaperRating->updateAll(array('WallpaperRating.is_deleted' => 1), array('WallpaperRating.wallpaper_id' => $wallpaperArr));
                        $this->WallpaperRating->updateAll(array('WallpaperRating.is_deleted' => 1), array('WallpaperRating.user_id' => $spamData['SpamReport']['component_id']));

                        /* Delete Wallpaper Comments */
                        $this->WallpaperComment->updateAll(array('WallpaperComment.is_deleted' => 1), array('WallpaperComment.wallpaper_id' => $wallpaperArr));
                        $this->WallpaperComment->updateAll(array('WallpaperComment.is_deleted' => 1), array('WallpaperComment.user_id' => $spamData['SpamReport']['component_id']));

                        /* Delete Wallpaper Comments */
                        $this->WallpaperLike->updateAll(array('WallpaperLike.is_deleted' => 1), array('WallpaperLike.wallpaper_id' => $wallpaperArr));
                        $this->WallpaperLike->updateAll(array('WallpaperLike.is_deleted' => 1), array('WallpaperLike.user_id' => $spamData['SpamReport']['component_id']));

                        /* Delete Wallpaper Comments */
                        $this->WallpaperCustomInvite->updateAll(array('WallpaperCustomInvite.is_deleted' => 1), array('WallpaperCustomInvite.wallpaper_id' => $wallpaperArr));
                        $this->WallpaperCustomInvite->updateAll(array('WallpaperCustomInvite.is_deleted' => 1), array('WallpaperCustomInvite.user_id' => $spamData['SpamReport']['component_id']));

                        /* Delete User feeds */
                        $this->ActivityFeed->updateAll(array('ActivityFeed.is_deleted' => 1), array('ActivityFeed.wallpaper_id' => $wallpaperArr, 'ActivityFeed.action Like' => '%wallpaper%'));
                        $this->ActivityFeed->updateAll(array('ActivityFeed.is_deleted' => 1), array('ActivityFeed.feed_user_id' => $spamData['SpamReport']['component_id']));
                    }
//                    $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'delete_user_report'),
//                        'fields' => array('subject', 'content', 'tags')));
//                    $arr1 = explode(',', $templateDetail['Template']['tags']);
//                    $arr2 = array($userArr['User']['full_name'], $userArr['User']['user_name']);
//                    $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
//                    try {
//                        if (strpos($userArr['User']['email'], 'example') == false) {
//                            if (EMAIL_SETTING != "mendrillAPI") {
//                                $this->send_email($templateDetail['Template']['subject'], $emailData, $userArr['User']['email'], 'mail_temp');
//                            } else {
//                                $emailData = $this->addHdrFtr($emailData);
//                                $this->mandrillEmailToArr[] = array("email" => $userArr['User']['email'], "name" => $userArr['User']['full_name'], "type" => 'to');
//                                $varArr = array("name" => "EMAIL", "content" => $emailData);
//                                $this->mandrillEmailVarsArr[] = array("rcpt" => $userArr['User']['email'], "vars" => array($varArr));
//                                $this->mandrill_api_email($templateDetail['Template']['subject'], $this->mandrillEmailToArr, $this->mandrillEmailVarsArr);
//                                $this->mandrillEmailVarsArr = null;
//                                $this->mandrillEmailToArr = null;
//                            }
//                        }
//                    } catch (Exception $e) {
//                        
//                    }
                }

                $this->Session->setFlash('User successfully deleted.', 'flashSuccess');
                $this->redirect("/spam_reports/report_users");
            } else {
                $this->Session->setFlash('User was already deleted.', 'flashError');
                $this->redirect("/spam_reports/report_users");
            }
        } else {
            $this->Session->setFlash('No data posted', 'flashError');
            $this->redirect("/spam_reports/report_users");
        }
    }

    public function show_user_detail($user_name = null) {
        try {
            $useArr = $this->User->find('first', array('conditions' => array('User.user_name' => $user_name)));
            if (!empty($useArr)) {
                $user_id = $useArr['User']['id'];
                $this->set('userArr', $useArr);

                $UserFollowing = $this->UserFollower->find('count', array('conditions' => array('UserFollower.follower_id' => $user_id)));
                $this->set('UserFollowing', $UserFollowing);
                $UserFollower = $this->UserFollower->find('count', array('conditions' => array('UserFollower.following_id' => $user_id)));
                $this->set('UserFollower', $UserFollower);

                $friendsCount = $this->Contact->find('count', array(
                    'conditions' => array("OR" => array(
                            array('Contact.from_user_id' => $user_id),
                            array('Contact.to_user_id' => $user_id)
                        )
                    ),
                    'Contact.request_is_attended' => 1,
                    'Contact.type' => "C"
                        )
                );
                $this->set('friendsCount', $friendsCount);
                $wallpaperArr = $this->Wallpaper->find('all', array('conditions' => array('Wallpaper.user_id' => $user_id)));
                $this->set('wallpaperArr', $wallpaperArr);
            } else {
                $this->Session->setFlash('User is already deleted.', 'flashError');
                $this->redirect("/report_users");
            }
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/report_users");
        }
        $this->set('title_for_layout', 'Users');
    }

    public function ignore_comment_report($spam_id = null) {
        $spamData = $this->SpamReport->find('first', array('conditions' => array('SpamReport.id' => $spam_id)));
        if (!empty($spamData)) {
            $spamData['SpamReport']['is_attended'] = 1;
            $spamData['SpamReport']['is_ignored'] = 1;
            $this->SpamReport->save($spamData);
            $this->Session->setFlash('Successfully ignored.', 'flashSuccess');
            $this->redirect("/spam_reports/report_comments");
        } else {
            $this->Session->setFlash('Not found.', 'flashError');
            $this->redirect("/spam_reports/report_comments");
        }
    }

    public function ignore_wallpaper_report($spam_id = null) {
        $spamData = $this->SpamReport->find('first', array('conditions' => array('SpamReport.id' => $spam_id)));
        if (!empty($spamData)) {
            $spamData['SpamReport']['is_attended'] = 1;
            $spamData['SpamReport']['is_ignored'] = 1;
            $this->SpamReport->save($spamData);
            $this->Session->setFlash('Successfully ignored.', 'flashSuccess');
            $this->redirect("/spam_reports/report_wallpapers");
        } else {
            $this->Session->setFlash('Not found.', 'flashError');
            $this->redirect("/spam_reports/report_wallpapers");
        }
    }

    public function ignore_user_report($spam_id = null) {
        $spamData = $this->SpamReport->find('first', array('conditions' => array('SpamReport.id' => $spam_id)));
        if (!empty($spamData)) {
            $spamData['SpamReport']['is_attended'] = 1;
            $spamData['SpamReport']['is_ignored'] = 1;
            $this->SpamReport->save($spamData);
            $this->Session->setFlash('Successfully ignored.', 'flashSuccess');
            $this->redirect("/spam_reports/report_users");
        } else {
            $this->Session->setFlash('Not found.', 'flashError');
            $this->redirect("/spam_reports/report_users");
        }
    }

}
