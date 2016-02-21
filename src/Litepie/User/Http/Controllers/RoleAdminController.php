<?php
namespace Litepie\User\Http\Controllers;

use Form;
use Response;
use Litepie\User\Models\Role;
use App\Http\Controllers\AdminController as AdminController;
use Litepie\User\Http\Requests\RoleAdminRequest;
use Litepie\Contracts\User\RoleRepository;
use Litepie\Contracts\User\PermissionRepository;


/**
 *
 * @package Role
 */
class RoleAdminController extends AdminController
{
    /**
     * @var Permissions
     *
     */
    protected $permission;

    /**
     * Initialize role controller
     * @param type RoleRepository $role
     * @return type
     */
    public function __construct(RoleRepository $role,
                                PermissionRepository $permission)
    {
        $this->permission = $permission;
        $this->repository = $role;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(RoleAdminRequest $request)
    {

        $roles  = $this->repository->setPresenter('\\Lavalite\\User\\Repositories\\Presenter\\RoleListPresenter')->paginate(NULL, ['*']);
        $this   ->theme->prependTitle(trans('user.role.names').' :: ');
        $view   = $this->theme->of('user::admin.role.index')->render();

        $this->responseCode = 200;
        $this->responseMessage = trans('messages.success.loaded', ['Module' => 'Role']);
        $this->responseData = $roles['data'];
        $this->responseMeta = $roles['meta'];
        $this->responseView = $view;
        $this->responseRedirect = '';
        return $this->respond($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Request  $request
     * @param  int  $id
     *
     * @return Response
     */
    public function show(RoleAdminRequest $request, Role $role)
    {

         if (!$role->exists) {
            $this->responseCode = 404;
            $this->responseMessage = trans('messages.success.notfound', ['Module' => 'Role']);
            $this->responseData = $role;
            $this->responseView = view('user::admin.role.new');
            return $this -> respond($request);
        }
        $permissions        = $this->permission->groupedPermissions(true);
        $rolePermissions    = [];
        Form::populate($role);
        $this->responseCode = 200;
        $this->responseMessage = trans('messages.success.loaded', ['Module' => 'Role']);
        $this->responseData = $role;
        $this->responseView = view('user::admin.role.show', compact('role', 'permissions', 'rolePermissions'));

        return $this -> respond($request);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(RoleAdminRequest $request)
    {

        $role = $this->repository->newInstance([]);
        Form::populate($role);
        $permissions  = $this->permission->groupedPermissions(true);
        $this->responseCode = 200;
        $this->responseMessage = trans('messages.success.loaded', ['Module' => 'Role']);
        $this->responseData = $role;
        $this->responseView = view('user::admin.role.create', compact('role', 'permissions'));
        return $this -> respond($request);

    }

    /**
     * Display the specified resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(RoleAdminRequest $request)
    {

         try {

            $attributes = $request->all();
            $role = $this->repository->create($attributes);
            $this->responseCode = 201;
            $this->responseMessage = trans('messages.success.created', ['Module' => 'Role']);
            $this->responseData = $role;
            $this->responseRedirect = trans_url('/admin/user/role/'.$role->getRouteKey());
            return $this -> respond($request);

        } catch (Exception $e) {
            $this->responseCode = 400;
            $this->responseMessage = $e->getMessage();
            return $this -> respond($request);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function edit(RoleAdminRequest $request, Role $role)
    {

        $permissions  = $this->permission->groupedPermissions(true);
        Form::populate($role);
        $rolePermissions    = [];
        $this->responseCode = 200;
        $this->responseMessage = trans('messages.success.loaded', ['Module' => 'Role']);
        $this->responseData = $role;
        $this->responseView = view('user::admin.role.edit', compact('role', 'permissions', 'rolePermissions'));

        return $this -> respond($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(RoleAdminRequest $request, Role $role)
    {
         try {

            $attributes = $request->all();
            $this->repository->update($attributes, $role->getRouteKey());
            $this->responseCode = 204;
            $this->responseMessage = trans('messages.success.updated', ['Module' => 'Role']);
            $this->responseRedirect = trans_url('/admin/user/role/'.$role->getRouteKey());
            return $this -> respond($request);

        } catch (Exception $e) {

            $this->responseCode = 400;
            $this->responseMessage = $e->getMessage();
            $this->responseRedirect = trans_url('/admin/user/role/'.$role->getRouteKey());

            return $this -> respond($request);
        }
    }

    /**
     * Remove the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(RoleAdminRequest $request, Role $role)
    {
        try {

            $t = $role->delete();
            $this->responseCode = 202;
            $this->responseMessage = trans('messages.success.deleted', ['Module' => 'Role']);
            $this->responseData = $role;
            $this->responseRedirect = trans_url('/admin/user/role/0');

            return $this -> respond($request);

        } catch (Exception $e) {

            $this->responseCode = 400;
            $this->responseMessage = $e->getMessage();
            $this->responseRedirect = trans_url('/admin/user/role/'.$role->getRouteKey());

            return $this -> respond($request);

        }
    }
}
