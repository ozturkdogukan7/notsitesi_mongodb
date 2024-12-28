<?php

namespace App\Controllers;

use App\Models\NoteModel;
use App\Models\CommentModel;
use App\Models\CategoryModel;

class Notes extends BaseController
{
    protected $noteModel;
    protected $commentModel;
    protected $categoryModel;
    protected $session;

    public function __construct()
    {
        helper(['form', 'text']);
        $this->noteModel = new NoteModel();
        $this->commentModel = new CommentModel();
        $this->categoryModel = new CategoryModel();
        $this->session = session();
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }

        $data = [
            'title' => 'Notlarım',
            'notes' => $this->noteModel->select('notes.*, categories.name as category_name')
                                    ->join('categories', 'categories.id = notes.category_id', 'left')
                                    ->where('notes.user_id', $this->session->get('id'))
                                    ->findAll()
        ];

        return view('notes/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'category_id' => 'required|numeric|is_not_unique[categories.id]',
                'content' => 'required|min_length[10]'
            ];

            $messages = [
                'title' => [
                    'required' => 'Başlık alanı zorunludur.',
                    'min_length' => 'Başlık en az 3 karakter olmalıdır.',
                    'max_length' => 'Başlık en fazla 255 karakter olabilir.'
                ],
                'category_id' => [
                    'required' => 'Kategori seçimi zorunludur.',
                    'numeric' => 'Geçersiz kategori.',
                    'is_not_unique' => 'Seçilen kategori bulunamadı.'
                ],
                'content' => [
                    'required' => 'İçerik alanı zorunludur.',
                    'min_length' => 'İçerik en az 10 karakter olmalıdır.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                $data = [
                    'user_id' => session()->get('id'),
                    'category_id' => $this->request->getPost('category_id'),
                    'title' => $this->request->getPost('title'),
                    'content' => $this->request->getPost('content'),
                    'is_private' => $this->request->getPost('is_private') ? 1 : 0
                ];

                try {
                    $this->noteModel->insert($data);
                    $this->session->setFlashdata('message', 'Not başarıyla eklendi');
                    $this->session->setFlashdata('type', 'success');
                    return redirect()->to(base_url('notes'));
                } catch (\Exception $e) {
                    log_message('error', 'Not eklenirken hata: ' . $e->getMessage());
                    $this->session->setFlashdata('message', 'Not eklenirken bir hata oluştu. Lütfen tekrar deneyin.');
                    $this->session->setFlashdata('type', 'danger');
                    return redirect()->back()->withInput();
                }
            } else {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }
        }

        $data['categories'] = $this->categoryModel->findAll();
        return view('notes/create', $data);
    }

    public function edit($id)
    {
        $note = $this->noteModel->find($id);
        
        if (!$note || $note['user_id'] !== session()->get('id')) {
            $this->session->setFlashdata('message', 'Not bulunamadı veya düzenleme yetkiniz yok');
            $this->session->setFlashdata('type', 'danger');
            return redirect()->to(base_url('notes'));
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'title' => 'required|min_length[3]',
                'category_id' => 'required|numeric',
                'content' => 'required|min_length[10]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'category_id' => $this->request->getPost('category_id'),
                    'title' => $this->request->getPost('title'),
                    'content' => $this->request->getPost('content'),
                    'is_private' => $this->request->getPost('is_private') ? 1 : 0
                ];

                try {
                    $this->noteModel->update($id, $data);
                    $this->session->setFlashdata('message', 'Not başarıyla güncellendi');
                    $this->session->setFlashdata('type', 'success');
                    return redirect()->to(base_url('notes'));
                } catch (\Exception $e) {
                    log_message('error', 'Not güncellenirken hata: ' . $e->getMessage());
                    $this->session->setFlashdata('message', 'Not güncellenirken bir hata oluştu');
                    $this->session->setFlashdata('type', 'danger');
                    return redirect()->back()->withInput();
                }
            }

            return redirect()->back()->withInput();
        }

        $data['note'] = $note;
        $data['categories'] = $this->categoryModel->findAll();
        return view('notes/edit', $data);
    }

    public function delete($id)
    {
        $note = $this->noteModel->find($id);
        
        if (!$note || $note['user_id'] !== session()->get('id')) {
            $this->session->setFlashdata('message', 'Not bulunamadı veya silme yetkiniz yok');
            $this->session->setFlashdata('type', 'danger');
            return redirect()->to(base_url('notes'));
        }

        try {
            $this->noteModel->delete($id);
            $this->session->setFlashdata('message', 'Not başarıyla silindi');
            $this->session->setFlashdata('type', 'success');
        } catch (\Exception $e) {
            log_message('error', 'Not silinirken hata: ' . $e->getMessage());
            $this->session->setFlashdata('message', 'Not silinirken bir hata oluştu');
            $this->session->setFlashdata('type', 'danger');
        }

        return redirect()->to(base_url('notes'));
    }

    public function addComment()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lütfen önce giriş yapın'
            ])->setStatusCode(401);
        }

        try {
            $noteId = $this->request->getPost('note_id');
            $comment = $this->request->getPost('comment');
            
            // Note ID'nin geçerli olduğunu kontrol et
            $note = $this->noteModel->find($noteId);
            if (!$note) {
                throw new \Exception('Not bulunamadı');
            }

            $data = [
                'note_id' => (int)$noteId,
                'user_id' => (int)$this->session->get('id'),
                'username' => $this->session->get('username'),
                'comment' => $comment,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->commentModel->insert($data);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Yorum başarıyla eklendi'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Comment Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getComments($noteId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Lütfen önce giriş yapın'
            ])->setStatusCode(401);
        }

        try {
            $comments = $this->commentModel->findByNoteId((int)$noteId);
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get Comments Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function testMongo()
    {
        try {
            $data = [
                'note_id' => 1,
                'user_id' => 1,
                'username' => 'test_user',
                'comment' => 'Test comment',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->commentModel->insert($data);
            var_dump($result);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
