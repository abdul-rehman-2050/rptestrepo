<?php
namespace App\Repositories;

use App\Notification;
use Illuminate\Validation\ValidationException;

class SuperNotificationRepository
{
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }



    public function create($request, $register = 0)
    {

        $params = $request->all();
        $params['created_by'] = \Auth::user()->id;
        $notification = $this->notification->forceCreate($params);
        return $notification;
    }


    public function update(Notification $notification, $params)
    {
        $params = $params->all();
        $notification->forceFill($params, 'update')->save();
        return $notification;
    }

       
    public function findOrFail($id)
    {
        $notification = $this->notification->find($id);

        if (! $notification) {
            throw ValidationException::withMessages(['message' => trans('notification.could_not_find')]);
        }

        return $notification;
    }

    public function delete(Notification $notification)
    {
        return $notification->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->notification->whereIn('id', $ids)->delete();
    }
}
