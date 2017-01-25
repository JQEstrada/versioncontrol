<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TempfileChanges Controller
 *
 * @property \App\Model\Table\TempfileChangesTable $TempfileChanges
 */
class TempfileChangesController extends AppController
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
        $tempfileChanges = $this->paginate($this->TempfileChanges);

        $this->set(compact('tempfileChanges'));
        $this->set('_serialize', ['tempfileChanges']);
    }

    /**
     * View method
     *
     * @param string|null $id Tempfile Change id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tempfileChange = $this->TempfileChanges->get($id, [
            'contain' => ['Files', 'Versions']
        ]);

        $this->set('tempfileChange', $tempfileChange);
        $this->set('_serialize', ['tempfileChange']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tempfileChange = $this->TempfileChanges->newEntity();
        if ($this->request->is('post')) {
            $tempfileChange = $this->TempfileChanges->patchEntity($tempfileChange, $this->request->data);
            if ($this->TempfileChanges->save($tempfileChange)) {
                $this->Flash->success(__('The tempfile change has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The tempfile change could not be saved. Please, try again.'));
            }
        }
        $files = $this->TempfileChanges->Files->find('list', ['limit' => 200]);
        $versions = $this->TempfileChanges->Versions->find('list', ['limit' => 200]);
        $this->set(compact('tempfileChange', 'files', 'versions'));
        $this->set('_serialize', ['tempfileChange']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Tempfile Change id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tempfileChange = $this->TempfileChanges->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tempfileChange = $this->TempfileChanges->patchEntity($tempfileChange, $this->request->data);
            if ($this->TempfileChanges->save($tempfileChange)) {
                $this->Flash->success(__('The tempfile change has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The tempfile change could not be saved. Please, try again.'));
            }
        }
        $files = $this->TempfileChanges->Files->find('list', ['limit' => 200]);
        $versions = $this->TempfileChanges->Versions->find('list', ['limit' => 200]);
        $this->set(compact('tempfileChange', 'files', 'versions'));
        $this->set('_serialize', ['tempfileChange']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Tempfile Change id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tempfileChange = $this->TempfileChanges->get($id);
        if ($this->TempfileChanges->delete($tempfileChange)) {
            $this->Flash->success(__('The tempfile change has been deleted.'));
        } else {
            $this->Flash->error(__('The tempfile change could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
