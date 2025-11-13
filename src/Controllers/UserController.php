<?php
namespace Src\Controllers;
use Src\Repositories\UserRepository;
use Src\Validation\Validator;
class UserController extends BaseController
{
    public function index()
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 10);

        $repo = new UserRepository($this->cfg);
        $data = $repo->paginate(max(1, $page), min(100, max(1, $perPage)));

        $this->ok($data);
    }
    public function show($id)
    {
        $repo = new UserRepository($this->cfg);
        $user = $repo->find((int) $id);

        if ($user) {
            $this->ok($user);
        } else {
            $this->error(404, 'User not found');
        }
    }
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $v = Validator::make($input, [
            'name'     => 'required|min:3|max:100',
            'email'    => 'required|email|max:150',
            'password' => 'required|min:6|max:72',
            'role'     => 'enum:user,admin'
        ]);
        if ($v->fails()) {
            return $this->error(422, 'Validation error', $v->errors());
        }
        $hash = password_hash($input['password'], PASSWORD_DEFAULT);
        $repo = new UserRepository($this->cfg);
        try {
            $this->ok(
                $repo->create(
                    $input['name'],
                    $input['email'],
                    $hash,
                    $input['role'] ?? 'user'
                ),
                201
            );
        } catch (\Throwable $e) {
            $this->error(400, 'Create failed', ['details' => $e->getMessage()]);
        }
    }
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $v = Validator::make($input, [
            'name'  => 'required|min:3|max:100',
            'email' => 'required|email|max:150',
            'role'  => 'enum:user,admin'
        ]);
        if ($v->fails()) {
            return $this->error(422, 'Validation error', $v->errors());
        }
        $repo = new UserRepository($this->cfg);
        $user = $repo->update((int) $id, $input['name'], $input['email'], $input['role']);

        $this->ok($user);
    }
    public function destroy($id)
    {
        $repo = new UserRepository($this->cfg);
        $deleted = $repo->delete((int) $id);

        if ($deleted) {
            $this->ok(['deleted' => true]);
        } else {
            $this->error(400, 'Delete failed');
        }
    }
}