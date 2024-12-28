<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\NoteModel;

class Admin extends BaseController
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
        if (!$this->session->get('is_admin')) {
            return redirect()->to(base_url('notes'));
        }

        $data['users'] = $this->authModel->findAll();
        return view('admin/users', $data);
    }

    public function notes()
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to(base_url('notes'));
        }

        $data['notes'] = $this->noteModel->getAllNotes();
        return view('admin/notes', $data);
    }

    public function deleteUser($id)
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to(base_url('notes'));
        }

        $this->authModel->delete($id);
        $this->session->setFlashdata('message', 'Kullanıcı başarıyla silindi');
        $this->session->setFlashdata('type', 'success');
        return redirect()->to(base_url('admin'));
    }

    public function deleteNote($id)
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to(base_url('notes'));
        }

        $this->noteModel->delete($id);
        $this->session->setFlashdata('message', 'Not başarıyla silindi');
        $this->session->setFlashdata('type', 'success');
        return redirect()->to(base_url('admin/notes'));
    }
}
