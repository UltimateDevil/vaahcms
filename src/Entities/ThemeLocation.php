<?php namespace WebReinvent\VaahCms\Entities;

use Illuminate\Database\Eloquent\Model;
use VaahCms\Modules\Cms\Entities\FormField;
use VaahCms\Modules\Cms\Entities\FormGroup;
use VaahCms\Modules\Cms\Entities\Menu;
use VaahCms\Modules\Cms\Entities\MenuItem;


class ThemeLocation extends Model {

    //-------------------------------------------------
    protected $table = 'vh_theme_locations';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'vh_theme_id',
        'type',
        'name',
        'slug',
    ];

    //-------------------------------------------------
    protected $appends  = [

    ];

    //-------------------------------------------------

    //-------------------------------------------------
    public function setSlugAttribute( $value ) {
        $this->attributes['slug'] = str_slug( $value );
    }
    //-------------------------------------------------
    public function scopeSlug( $query, $slug ) {
        return $query->where( 'slug', $slug );
    }
    //-------------------------------------------------
    public function scopeTheme( $query, $theme_id ) {
        return $query->where( 'vh_theme_id', $theme_id );
    }
    //-------------------------------------------------
    public function scopeType( $query, $type) {
        return $query->where( 'type', $type );
    }
    //-------------------------------------------------
    public function scopeCreatedBetween( $query, $from, $to ) {
        return $query->whereBetween( 'created_at', array( $from, $to ) );
    }
    //-------------------------------------------------
    public function scopeUpdatedBetween( $query, $from, $to ) {
        return $query->whereBetween( 'updated_at', array( $from, $to ) );
    }

    //-------------------------------------------------
    public function scopeDeletedBetween( $query, $from, $to ) {
        return $query->whereBetween( 'deleted_at', array( $from, $to ) );
    }

    //-------------------------------------------------

    //-------------------------------------------------

    //-------------------------------------------------
    public static function getMenuLocationsForAssets()
    {

        $list = ThemeLocation::theme(vh_get_theme_id())
            ->type('menu')
            ->get();

        $result = [];
        $i = 0;
        foreach ($list as $item)
        {

            $result[$i]['name'] = $item->name;
            $result[$i]['id'] = $item->id;

            $i++;
        }

        return $result;

    }
    //---------------------------------------------------------------------------
    public static function getLocationData($slug, $html=false, $type='bootstrap')
    {
        $data = [];

        $location = ThemeLocation::theme(vh_get_theme_id())
            ->slug($slug)
            ->first();

        if(!$location)
        {
            return false;
        }

        switch ($location->type)
        {
            case 'menu':

                $data = ThemeLocation::getMenuLocation($location, $html, $type);

                break;

            //---------------------------------------
            case 'sidebar':

                break;
            //---------------------------------------
            //---------------------------------------
            //---------------------------------------

        }


        return $data;
    }

    //---------------------------------------------------------------------------
    public static function getMenuLocation($location, $html, $type)
    {

        $menus_count = Menu::where('vh_theme_location_id', $location->id)
            ->count();

        if($menus_count == 0)
        {
            return false;
        }

        $result = [];
        $menu_html = "";
        $i = 0;


        $find_menus = Menu::where('vh_theme_location_id', $location->id)
            ->get();

        foreach ($find_menus as $menu)
        {
            $result[$i] = MenuItem::with(['page'])
                ->where('vh_menu_id', $menu->id)
                ->get()->toArray();


            if($html == true)
            {
                $menu_html .= ThemeLocation::getMenuHtml($result[$i], $type);
            }

            $i++;
        }

        if($html == true)
        {
            return $menu_html;
        } else
        {
            if(count($result) == 1)
            {
                return $result[0];
            } else
            {
                return $result;
            }
        }



    }
    //---------------------------------------------------------------------------
    public static function getMenuHtml($array, $type)
    {


        $html = "";
        switch($type)
        {
            case 'bootstrap':
                $html = '<ul class="navbar-nav">';
                $html .= get_bootstrap_menu($array, false);
                $html .= '</ul>';
                break;

            case 'ulli':
                $html = '<ul>';
                $html .= get_ulli_menu($array, false);
                $html .= '</ul>';
                break;
        }

        return $html;
    }

    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------

}
