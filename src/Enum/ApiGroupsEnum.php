<?php

namespace App\Enum;

enum ApiGroupsEnum: string
{
    case PET_SELECT_MENU = 'app_select_menu';
    case PET_ADD = 'app_pet_web';
    case PET_SEARCH = 'app_pet_search';
    case PET_UPDATE = 'app_pet_update';
    case PET_DELETE = 'app_pet_delete';
    case USER_SELECT_MENU = 'app_user_menu';
    case USER_ADD = 'app_user_web';
    case USER_LIST = 'app_user_list';
    case USER_UPDATE = 'app_user_update';
}
