<?php

App::uses('AppController', 'Controller');

/**
 * Interests Controller
 *
 * @property Interest $Interest
 */
class InterestsController extends AppController {
    /*
     * Models
     * @var array
     */

    public $uses = array('Interest', 'User', 'WallpaperCustomInvite', 'WallpaperLike', 'WallpaperRating', 'Wallpaper', 'Contact');

    /**
     * Index action
     *
     * Lists all interests
     *
     * @return void
     */
    public function get_all_interests($accessKey = null, $userkey = null) {
        $interestList = $this->Interest->find("all", array(
            'fields' => array('id as interest_id', 'name', 'image'),
            'order' => array('name' => 'asc')));
        $interestData = array();
        foreach ($interestList as $interestList) {
            $interestData[] = $interestList['Interest'];
        }
        $this->apiOutputArr['Interest'] = $interestData;
        $this->apiResponseCode = API_CODE_SUCCESS;
    }

    public function search_interests($accessKey = null, $userkey = null) {
        $validationErrors = false;
        if (empty($this->request->data['search_name'])) {
            $this->apiErrors[] = 'Search name is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $interestList = $this->Interest->find("all", array(
                'fields' => array('id as interest_id', 'name', 'image'),
                'conditions' => array('Interest.name LIKE' => "%" . $this->request->data['search_name'] . "%")
                    )
            );
            $interestData = array();
            foreach ($interestList as $interestList) {
                $interestData[] = $interestList['Interest'];
            }
            $this->apiOutputArr['Interest'] = $interestData;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function get_all_interest_wallpapers($accessKey = null, $userkey = null, $interest_id = null, $page = null) {
        $validationErrors = false;
        if (empty($interest_id)) {
            $this->apiErrors[] = 'Interest id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }

        $followers = $this->getFollowersListArr($this->currentUser['id']);
        $contactsUserIdArr = $this->getContactsUserIdListArr($this->currentUser['id']);
        if (!$validationErrors) {
            $this->Wallpaper->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('full_name', 'user_name', 'avatar', 'show_full_name')
                    )
                ),
                'hasOne' => array(
                    'WallpaperCustomInvite' => array(
                        'className' => 'WallpaperCustomInvite',
                        'foreignKey' => 'wallpaper_id',
                        'fields' => array('user_id', 'wallpaper_id')
                    ),
                    'WallpaperLike' => array(
                        'className' => 'WallpaperLike',
                        'foreignKey' => 'wallpaper_id',
                        'conditions' => array('WallpaperLike.user_id' => $this->currentUser['id']),
                        'fields' => array('user_id')
                    ),
                    'WallpaperRating' => array(
                        'className' => 'WallpaperRating',
                        'foreignKey' => 'wallpaper_id',
                        'conditions' => array('WallpaperRating.user_id' => $this->currentUser['id']),
                        'fields' => array('rating')
                    )
                )
                    ), false
            );

            $this->paginate = array(
                'conditions' => array(
                    'Wallpaper.interest_id' => $interest_id,
                    'OR' => array(
                        'Wallpaper.privacy_type' => 0,
                        array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                        array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                        array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $this->currentUser['id'])
                    )
                ),
                'fields' => array('Wallpaper.id as wallpaper_id', 'User.id as user_id', 'User.full_name as full_name', 'User.user_name as user_name', 'User.show_full_name as show_full_name', 'User.avatar as avatar', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.thumb', 'Wallpaper.video_duration', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating', 'Contact.id'),
                'order' => array('Wallpaper.created' => 'DESC'),
                'joins' => array(
                    array(
                        'table' => 'contacts',
                        'alias' => 'Contact',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "OR" => array(
                                array('Contact.to_user_id' => 'Wallpaper.user_id', 'Contact.from_user_id' => $this->currentUser['id']),
                                array('Contact.from_user_id' => 'Wallpaper.user_id', 'Contact.to_user_id' => $this->currentUser['id'])
                            )
                        )
                    )
                ),
                'limit' => NORMAL_PAGINATION_LIMIT,
                'page' => $page
            );

            $SoapBoxes = $this->paginate('Wallpaper');
            $SoapBoxesData = array();

            if (!empty($SoapBoxes)) {
                foreach ($SoapBoxes as $SoapBox) {
                    if (empty($mySoapBox['Contact']['id']) && empty($mySoapBox['User']['show_full_name'])) {
                        $mySoapBox['User']['full_name'] = $mySoapBox['User']['user_name'];
                    }
                    $mergedArr = array_merge($SoapBox['User'], $SoapBox['Wallpaper']);
                    if (!empty($SoapBox['WallpaperLike']['user_id'])) {
                        $isLiked['is_liked'] = true;
                        $mergedArr = array_merge($mergedArr, $isLiked);
                    }
                    if (!empty($SoapBox['WallpaperRating']['rating'])) {
                        $isRated['is_rated'] = true;
                        $isRated['rating'] = $SoapBox['WallpaperRating']['rating'];
                        $mergedArr = array_merge($mergedArr, $isRated);
                    }
                    $SoapBoxesData[] = $mergedArr;
                }
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['Wallpaper']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            $this->apiOutputArr['SoapBoxes'] = $SoapBoxesData;
            /*             * ***** check next page ends ********* */
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

}
