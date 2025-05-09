<?php
// Admin Route
/*Route::group(['prefix'=>'admin','middleware' => ['auth','dashboard']], function() {
    Route::match(['get','post'],'/',function (){
        $module = ucfirst(htmlspecialchars('Dashboard'));
        $controller = ucfirst(htmlspecialchars($module));
        $class = "\\Modules\\$module\\Admin\\";
        $action = 'index';
        if(class_exists($class.$controller.'Controller') && method_exists($class.$controller.'Controller',$action)){
            return App::call($class.$controller.'Controller@'.$action,[]);
        }
        abort(404);
    });
    Route::match(['get','post'],'/module/{module}/{controller?}/{action?}/{param1?}/{param2?}/{param3?}',function ($module,$controller = '',$action = '',$param1 = '',$param2 = '',$param3 = ''){
        $module = ucfirst(htmlspecialchars($module));
        $controller = ucfirst(htmlspecialchars($controller));
        $class = "\\Modules\\$module\\Admin\\";
        if(!class_exists($class.$controller.'Controller')){
            $param3 = $param2;
            $param2 = $param1;
            $param1 = $action;
            $action = $controller;
            $controller = $module;
        }
        $action = $action ? $action : 'index';
        if(class_exists($class.$controller.'Controller') && method_exists($class.$controller.'Controller',$action)){
            $p = array_values(array_filter([$param1,$param2,$param3]));
            return App::call($class.$controller.'Controller@'.$action,$p);
//            return App::make($class.$controller.'Controller')->callAction($action,$p);
        }
        abort(404);
    });
});*/

// Flight Provider Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'dashboard']], function() {
    Route::group(['prefix' => 'flight'], function () {
        Route::get('/provider', 'App\Http\Controllers\Admin\FlightProviderController@index')->name('flight.admin.provider.index');
        Route::post('/provider', 'App\Http\Controllers\Admin\FlightProviderController@update')->name('flight.admin.provider.update');
        
        // Flight Settings Routes
        Route::get('/settings', 'App\Http\Controllers\Admin\FlightSettingController@index')->name('flight.admin.settings.index');
        Route::post('/settings', 'App\Http\Controllers\Admin\FlightSettingController@update')->name('flight.admin.settings.update');
    });
});