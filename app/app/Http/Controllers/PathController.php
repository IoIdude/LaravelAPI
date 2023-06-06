<?php

namespace App\Http\Controllers;

use App\Models\Path;
use App\Models\Role;
use App\Models\Type;
use App\Common\FormatSizeFile;
use App\Common\MonthList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

date_default_timezone_set('Europe/Moscow');

class PathController extends Controller
{
    function createFolder(Request $request)
    {
        //Storage::disk('public')->put('/folders', $request['preview_image']);  //добавление файлов
        $path_created = Storage::makeDirectory($request->route);

        $type_id = Type::where('name', 'folder')->first();

        Path::create(['route' => $request->route, 'type_id' => $type_id->id]);

        $path_db = Path::where('route', $request->route)->first();
        //dd($request->role);
        $roles = $request->role;
        //return $roles;
//        $arr = [];
//        foreach ($roles as &$item)
//        {
//            $arr[] = $item;
//            return $arr;
//        }

        foreach ($roles as &$item)
        {
            $role = Role::where('id', $item)->first();
            $role->paths()->attach($path_db);
        }
        unset($item);

        return $role;
    }

    function getRolePath(Request $request)
    {
        $cur_role = Role::where('name', $request->role)->first();
        $path = Path::where('route', $request->route)->first();
        $res = $cur_role->paths()->where('path_id', $path->id)->first();

        return $res->id;
    }

    function setRoleToPath(Request $request)
    {
        $path_db = Path::where('route', $request->route)->first();
        $path_db->roles()->detach();

        $roles = $request->role;
        foreach ($roles as &$item_role)
        {
            $role = Role::where('id', $item_role)->first();
            $role->paths()->attach($path_db);
        }
        unset($item_role);

        return $roles;
    }

    function deleteFolder(Request $request)
    {
        $folders = Storage::disk('public')->allDirectories($request->route);
        $files = Storage::disk('public')->allFiles($request->route);
        Path::whereIn('route', $folders)->orWhereIn('route', $files)->delete();
        //dd($paths);
        Path::where('route', $request->route)->first()->delete();
        //Storage::disk('public')->put('/folders', $request['preview_image']);  //добавление файлов
        Storage::deleteDirectory($request->route);

        return response(['done']);
    }

    function checkFolder(Request $request)
    {
        $folders = Storage::disk('public')->Directories($request->route);
        return response($folders);
    }

    function checkFile(Request $request)
    {
        $files = Storage::disk('public')->Files($request->route);
        return response($files);
    }

    function getFiles(Request $request)
    {
        $folders = Storage::disk('public')->directories($request->path);
        $files = Storage::disk('public')->files($request->path);


        return response(['current_folder' => $request->path, 'files' => array_merge($folders, $files)]);
    }

    function getFilesByRole(Request $request)
    {
        if ($request->role_id == 0) return response(['current_folder' => $request->path, 'files' => []]);

        $folders = Storage::disk('public')->directories($request->path);
        $files = Storage::disk('public')->files($request->path);

        $role = Role::where('id', $request->role_id)->first();
        $ids_paths_db = $role->paths->pluck('id');
        $paths_db = Path::findMany($ids_paths_db)->pluck('route')->toArray();

        $arr_paths = array_values(array_intersect(array_merge($folders, $files), $paths_db));

        return response(['current_folder' => $request->path, 'files' => $arr_paths]);
    }

    function createFile(Request $request)
    {
        //dd($request->path);
        //Storage::disk('public')->put($request->path, $request['file']);  //добавление файлов

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $validator = Validator::make($request->all(), [
                'file' => [
                        'max:9000',
                        'required',
                        static function ($attribute, $file, $fail) {
                            $free_space = disk_free_space( Storage::disk('public')->path('/'));
                            $free_space -= 5000000000;
                            if ($file->getSize() > $free_space) {
                                if ($free_space > 0)
                                    $fail('На диске осталось: ' . FormatSizeFile::formatSizeUnits($free_space));
                                else
                                    $fail('На диске осталось: ' . FormatSizeFile::formatSizeUnits(0));
                            }
                        },
                    ]
                ],
                [
                    'max' => 'Файл весит больше 9 гигабайт'
                ]
            );

            if ($validator->fails()) {
                return response($validator->messages()->first());
            }

            $filename = $file->getClientOriginalName();
            $file->storeAs($request->path, $filename);

            $type_id = Type::where('name', 'file')->first();
            if ($request->path == "/") {
                Path::create(['route' => $filename, 'type_id' => $type_id->id]);
                $path_file_db = Path::where('route', $filename)->first();
                $role = Role::where('name', 'Admin')->first();
                $role->paths()->attach($path_file_db);

                return response('');
            }

            Path::create(['route' => $request->path . $filename, 'type_id' => $type_id->id]);
            $path_folder_db = Path::where('route', rtrim($request->path, '/'))->first();
            $path_file_db = Path::where('route', $request->path . $filename)->first();

            $roles = $path_folder_db->roles()->get();

            foreach ($roles as &$item) {
                $role = Role::where('id', $item->id)->first();
                $role->paths()->attach($path_file_db);
            }
            unset($item);

            return response('');
        }

        return response('');
    }

    function deleteFile(Request $request)
    {
        Storage::disk('public')->delete($request->path);
       // $files = Storage::disk('public')->files($request->path);
        $file = Path::where('route', $request->path)->first()->delete();

        return $file;
    }

    function fileInfo(Request $request)  // настроить установку ролей на создаваемые файлы и папки в папке
    {
        $path_db = Path::where('route', $request->route)->first();
        $type_file = Type::where('name', 'file')->first();

        if ($path_db->type_id == $type_file->id) {
            $time_upd = date("j F Y \в H:i:s", Storage::lastModified($request->route));
            $time_create = Carbon::parse($path_db->created_at)->format("j F Y \в H:i:s");

            $path = $request->route;
            $name = explode("/", $request->route);
            $size = Storage::size($request->route);
            $type = explode(".", $request->route);
            $time_upd_replace = str_replace(date("F", Storage::lastModified($request->route)), MonthList::getMonth(date("F", Storage::lastModified($request->route))), $time_upd);
            $roles = $path_db->roles()->get();
            $time_create_replace = str_replace(Carbon::parse($path_db->created_at)->format("F"), MonthList::getMonth(Carbon::parse($path_db->created_at)->format("F")), $time_create);

            return response(['size' => FormatSizeFile::formatSizeUnits($size), 'time_create' => $time_create_replace, 'time_upd' => $time_upd_replace, 'name' => $name[count($name) - 1], 'path' => $path, 'type' => 'Файл '.$type[count($type) - 1], 'roles' => $roles]);
        }
        else
        {
            $size = 0;

            foreach(Storage::disk('public')->allFiles($request->route) as $file)
            {
                $size += Storage::size($file);
            }

            $time_upd = date("j F Y \в H:i:s", Storage::lastModified($request->route));
            $time_create = Carbon::parse($path_db->created_at)->format("j F Y \в H:i:s");

            $time_create_replace = str_replace(Carbon::parse($path_db->created_at)->format("F"), MonthList::getMonth(Carbon::parse($path_db->created_at)->format("F")), $time_create);
            $time_upd_replace = str_replace(date("F", Storage::lastModified($request->route)), MonthList::getMonth(date("F", Storage::lastModified($request->route))), $time_upd);
            $type = 'Папка';
            $roles = $path_db->roles()->get();
            $name = explode("/", $request->route);
            $path = $request->route;

            return response(['size' => FormatSizeFile::formatSizeUnits($size), 'time_create' => $time_create_replace, 'time_upd' => $time_upd_replace, 'name' => $name[count($name) - 1], 'path' => $path, 'type' => $type, 'roles' => $roles]);
        }
    }

    function rename(Request $request)
    {
        if ($request->type == 'file')
        {
            if ($request->route == '/') {
                $path_parts = pathinfo(storage_path($request->route_name));
                $new_route = str_replace($path_parts['filename'], $request->new_name, $request->route_name);
            }
            else
            {
                $type = explode(".", $request->route_name);
                $type = $type[count($type) - 1];
                $new_route = $request->route.$request->new_name.'.'.$type;
            }
        }
        elseif ($request->type == 'folder')
        {
            if ($request->route == '/')
                $new_route = $request->new_name;
            else
                $new_route = $request->route.$request->new_name;

            $folders = Storage::disk('public')->allDirectories($request->route_name);
            $files = Storage::disk('public')->allFiles($request->route_name);
            $arr = array_merge($folders, $files);
            foreach ($arr as $item) {
                $file_name = explode("/", $item);
                $file_name = $file_name[count($file_name) - 1];
                Path::where('route', $item)->first()->update(['route' => $new_route.'/'.$file_name]);
            }
        }

        Path::where('route', $request->route_name)->first()->update(['route' => $new_route]);
        Storage::move($request->route_name, $new_route);
    }

    function move(Request $request) // move file/folder to folder, change route data in db
    {
        if ($request->type == 'folder')
        {
            $folders = Storage::disk('public')->allDirectories($request->path);
            $files = Storage::disk('public')->allFiles($request->path);
            $arr = array_merge($folders, $files);

            foreach ($arr as $item) {
                Path::where('route', $item)->first()->update(['route' => $request->move_to.'/'.$item]);
            }
        }

        if ($request->curr_path == '/') {
            Storage::move($request->path, $request->move_to.'/'.$request->path);
            Path::where('route', $request->path)->first()->update(['route' => $request->move_to.'/'.$request->path]);
        }
        else
        {
            $new_route = str_replace($request->curr_path, '', $request->path);
            Storage::move($request->path, $request->move_to.'/'.$new_route);
            Path::where('route', $request->path)->first()->update(['route' => $request->move_to.'/'.$new_route]);
        }
    }

    function saveZip(Request $request)
    {
        if ($request->type == 'folder') {
            $folder_name = explode("/", $request->route);
            $folder_name = $folder_name[count($folder_name) - 1];
            $zip_file = $folder_name.'-arch.zip';
            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $path = '/app/public/'.$request->route;

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(storage_path($path)));

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $file_info = pathinfo($file->getRealPath());

                    $zip->addFile($filePath, $folder_name . '/' . $file_info['basename']);
                }
            }
            $zip->close();
        }
        else
        {
            $file_name = pathinfo(storage_path($request->route));
            $zip_file = $file_name['filename'].'-arch.zip'; // Name of our archive to download

            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $invoice_file = '/app/public/'.$request->route;

            $zip->addFile(storage_path($invoice_file), $request->route);
            $zip->close();
        }

        return response()->download($zip_file);
    }

    function saveFile(Request $request)
    {
        //$file = Path::where('route', $request->path)->first();
        $file = Storage::disk('public')->download($request->path);

        return $file;//response()->download(public_path($request->path), 'User Image');
    }
}
