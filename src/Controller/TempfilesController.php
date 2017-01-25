<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use ftpal\ftpal;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Tempfiles Controller
 *
 * @property \App\Model\Table\TempfilesTable $Tempfiles
 */
class TempfilesController extends AppController
{


    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Projects']
        ];
        $tempfiles = $this->paginate($this->Tempfiles);

        $this->set(compact('tempfiles'));
        $this->set('_serialize', ['tempfiles']);
    }

    /**
     * View method
     *
     * @param string|null $id Tempfile id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tempfile = $this->Tempfiles->get($id, [
            'contain' => ['Projects']
        ]);

        $this->set('tempfile', $tempfile);
        $this->set('_serialize', ['tempfile']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Tempfile id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tempfile = $this->Tempfiles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tempfile = $this->Tempfiles->patchEntity($tempfile, $this->request->data);
            if ($this->Tempfiles->save($tempfile)) {
                $this->Flash->success(__('The tempfile has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The tempfile could not be saved. Please, try again.'));
            }
        }
        $projects = $this->Tempfiles->Projects->find('list', ['limit' => 200]);
        $this->set(compact('tempfile', 'projects'));
        $this->set('_serialize', ['tempfile']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Tempfile id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tempfile = $this->Tempfiles->get($id);
        if ($this->Tempfiles->delete($tempfile)) {
            $this->Flash->success(__('The tempfile has been deleted.'));
        } else {
            $this->Flash->error(__('The tempfile could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
