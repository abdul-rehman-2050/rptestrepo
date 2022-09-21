<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\NotificationRequest;
use App\Repositories\SuperNotificationRepository;
use App\Repositories\ActivityLogRepository;
use App\Notification;

class NotificationController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'notification';

    public function __construct(Request $request, SuperNotificationRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-notification');
        $query = Notification::search()
                ->selectRaw('id, message, from_date, till_date, created_at');

        return dataTable()->query($query)
            ->setFilters(
                'notifications.name', 'notifications.code'
            )
            ->get();
    }

    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }


    public function getAll()
    {
        return $this->ok(['notificationes'=>\App\Notification::get()]);
    }

    public function store(NotificationRequest $request)
    {
        $this->middleware('permission:create-notification');

        $notification = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $notification->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('notification.added')]);
    }


    public function update(NotificationRequest $request, $id) {
        $this->middleware('permission:edit-notification');

        $notification = $this->repo->findOrFail($id);
        $notification = $this->repo->update($notification, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $notification->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('notification.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-notification');

        $notification = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $notification->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($notification);

        return $this->success(['message' => trans('notification.deleted')]);
    }

}
