<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\NoteModel;

class Profile extends BaseController
{
    protected $authModel;
    protected $noteModel;
    protected $session;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->noteModel = new NoteModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userId = $this->session->get('id');
        $user = $this->authModel->find($userId);
        $notes = $this->noteModel->where('user_id', $userId)->findAll();

        $data = [
            'user' => $user,
            'notes' => $notes,
            'totalNotes' => count($notes),
            'privateNotes' => count(array_filter($notes, function($note) {
                return $note['is_private'] == 1;
            }))
        ];

        return view('profile/index', $data);
    }

    public function changePassword()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[6]',
                'confirm_password' => 'required|matches[new_password]'
            ];

            if ($this->validate($rules)) {
                $userId = $this->session->get('id');
                $user = $this->authModel->find($userId);

                if (password_verify($this->request->getPost('current_password'), $user['password'])) {
                    $this->authModel->update($userId, [
                        'password' => $this->request->getPost('new_password')
                    ]);

                    $this->session->setFlashdata('message', 'Şifreniz başarıyla güncellendi.');
                    $this->session->setFlashdata('type', 'success');
                    return redirect()->to(base_url('profile'));
                } else {
                    $this->session->setFlashdata('message', 'Mevcut şifreniz yanlış.');
                    $this->session->setFlashdata('type', 'danger');
                }
            }
        }

        return view('profile/change_password', ['validation' => $this->validator ?? null]);
    }
}
