<?php

namespace App\Http\Controllers;

use App\Models\Menu\AppMenu;
use App\Models\Menu\MenuMapping;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    use ApiResponser;

    public function GetMenuList()
    {
        try
        {
            $menus=AppMenu::all();
            return $menus;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function DeleteSingleMenu($id)
    {
        try
        {
            AppMenu::where('appMenuId',$id)->delete();
            return $this->successMsg('Successfully deleted');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function CreateMenu(Request $request)
    {
        try
        {
            $request->validate([
                'menuTitle'=>'required|string',
                'url'=>'required|string',
                'sortOrder'=>'required',
                'iconClass'=>'required|string',
                'addedBy'=>'required'
            ]);

            if(AppMenu::where(strtolower('menuTitle'),strtolower($request->menuTitle))->first()!=null){
                return $this->error('Duplicate Menu Title!',202,'duplicate');
            }else if(AppMenu::where(strtolower('url'),strtolower($request->url))->first()!=null){
                return $this->error('Duplicate Url!',202,'duplicate');
            }else if(AppMenu::where('sortOrder',$request->sortOrder)->first()!=null){
                return $this->error('Duplicate Order No!',202,'duplicate');
            }else if($request->sortOrder<=0){
                return $this->error('Order No. must greater than 0!',202,'error');
            }else{
                AppMenu::create([
                    'menuTitle'=>$request->menuTitle,
                    'url'=>$request->url,
                    'sortOrder'=>$request->sortOrder,
                    'iconClass'=>$request->iconClass,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully saved');
            }

        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function UpdateMenu(Request $request)
    {
        try
        {
            $request->validate([
                'appMenuId'=>'required',
                'menuTitle'=>'required|string',
                'url'=>'required|string',
                'sortOrder'=>'required',
                'iconClass'=>'required|string',
                'lastUpdatedBy'=>'required'
            ]);

            $objMenu=AppMenu::where('appMenuId',$request->appMenuId)->first();
            $objMenuTitleCheck=AppMenu::where(strtolower('menuTitle'),strtolower($request->menuTitle))->first();
            $objUrlCheck=AppMenu::where(strtolower('url'),strtolower($request->url))->first();
            $objSortOrderCheck=AppMenu::where('sortOrder',$request->sortOrder)->first();

            if($request->sortOrder<=0){
                return $this->error('Order No. must greater than 0!',202,'error');
            }else if($objMenuTitleCheck!=null && strtolower($objMenuTitleCheck->menuTitle)!=strtolower($objMenu->menuTitle)){
                return $this->error('Duplicate Menu Title!',202,'duplicate');
            }else if($objUrlCheck!=null && strtolower($objUrlCheck->url)!=strtolower($objMenu->url)){
                return $this->error('Duplicate Url!',202,'duplicate');
            }else if($objSortOrderCheck!=null && $objSortOrderCheck->sortOrder!=$objMenu->sortOrder){
                return $this->error('Duplicate order no!',202,'duplicate');
            }else{
                AppMenu::where('appMenuId',$request->appMenuId)
                ->update([
                    'menuTitle'=>$request->menuTitle,
                    'url'=>$request->url,
                    'sortOrder'=>$request->sortOrder,
                    'iconClass'=>$request->iconClass,
                    'lastUpdatedBy'=>$request->lastUpdatedBy,
                    'lastUpdatedDate'=>now()
                ]);
                return $this->successMsg('Successfully updated');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetSidebarMenu($roleId)
    {
        try
        {
            $sidebarMenus=DB::table('menumappings')
                            ->join('appmenus','menumappings.appMenuId','=','appmenus.appMenuId')
                            ->select('appmenus.*')
                            ->where('menumappings.userRoleId','=',$roleId)
                            ->get();
            return $sidebarMenus;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function GetAllMenu($userRoleId)
    {
        try
        {
            $allMenus=DB::select('SELECT m.appMenuId,m.menuTitle,m.url,m.iconClass,
            if((m.appMenuId=(select appMenuId from menumappings where appMenuId=m.appMenuId and userRoleId='.$userRoleId.')),true,false) isSelected FROM `appmenus` m;');
            return $allMenus;
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }

    public function MenuAssign(Request $request)
    {
        try
        {
            $objCheck=MenuMapping::where('userRoleId',$request->userRoleId)
                ->where('appMenuId',$request->menuId)
                ->first();
            
            if($objCheck==null){
                MenuMapping::create([
                    'userRoleId'=>$request->userRoleId,
                    'appMenuId'=>$request->menuId,
                    'addedBy'=>$request->addedBy
                ]);
                return $this->successMsg('Successfully assigned');
            }else{
                MenuMapping::where('menuMappingId',$objCheck->menuMappingId)->delete();
                return $this->successMsg('Successfully un-assigned');
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),202,'error');
        }
    }
}
