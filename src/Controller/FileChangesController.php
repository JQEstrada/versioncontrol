<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * FileChanges Controller
 *
 * @property \App\Model\Table\FileChangesTable $FileChanges
 */
class FileChangesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Files', 'Versions']
        ];
        $fileChanges = $this->paginate($this->FileChanges);

        $this->set(compact('fileChanges'));
        $this->set('_serialize', ['fileChanges']);
    }

    /**
     * View method
     *
     * @param string|null $id File Change id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $fileChange = $this->FileChanges->get($id, [
            'contain' => ['Files', 'Versions']
        ]);

        $this->set('fileChange', $fileChange);
        $this->set('_serialize', ['fileChange']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $fileChange = $this->FileChanges->newEntity();
        if ($this->request->is('post')) {
            $fileChange = $this->FileChanges->patchEntity($fileChange, $this->request->data);
            if ($this->FileChanges->save($fileChange)) {
                $this->Flash->success(__('The file change has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The file change could not be saved. Please, try again.'));
            }
        }
        $files = $this->FileChanges->Files->find('list', ['limit' => 200]);
        $versions = $this->FileChanges->Versions->find('list', ['limit' => 200]);
        $this->set(compact('fileChange', 'files', 'versions'));
        $this->set('_serialize', ['fileChange']);
    }

    /**
     * Edit method
     *
     * @param string|null $id File Change id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $fileChange = $this->FileChanges->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $fileChange = $this->FileChanges->patchEntity($fileChange, $this->request->data);
            if ($this->FileChanges->save($fileChange)) {
                $this->Flash->success(__('The file change has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The file change could not be saved. Please, try again.'));
            }
        }
        $files = $this->FileChanges->Files->find('list', ['limit' => 200]);
        $versions = $this->FileChanges->Versions->find('list', ['limit' => 200]);
        $this->set(compact('fileChange', 'files', 'versions'));
        $this->set('_serialize', ['fileChange']);
    }

    /**
     * Delete method
     *
     * @param string|null $id File Change id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $fileChange = $this->FileChanges->get($id);
        if ($this->FileChanges->delete($fileChange)) {
            $this->Flash->success(__('The file change has been deleted.'));
        } else {
            $this->Flash->error(__('The file change could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
