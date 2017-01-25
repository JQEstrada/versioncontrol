<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Procedures Controller
 *
 * @property \App\Model\Table\ProceduresTable $Procedures
 */
class ProceduresController extends AppController
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
        $procedures = $this->paginate($this->Procedures);

        $this->set(compact('procedures'));
        $this->set('_serialize', ['procedures']);
    }

    /**
     * View method
     *
     * @param string|null $id Procedure id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $procedure = $this->Procedures->get($id, [
            'contain' => ['Projects']
        ]);

        $this->set('procedure', $procedure);
        $this->set('_serialize', ['procedure']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $procedure = $this->Procedures->newEntity();
        if ($this->request->is('post')) {
            $procedure = $this->Procedures->patchEntity($procedure, $this->request->data);
            if ($this->Procedures->save($procedure)) {
                $this->Flash->success(__('The procedure has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The procedure could not be saved. Please, try again.'));
            }
        }
        $projects = $this->Procedures->Projects->find('list', ['limit' => 200]);
        $this->set(compact('procedure', 'projects'));
        $this->set('_serialize', ['procedure']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Procedure id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $procedure = $this->Procedures->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $procedure = $this->Procedures->patchEntity($procedure, $this->request->data);
            if ($this->Procedures->save($procedure)) {
                $this->Flash->success(__('The procedure has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The procedure could not be saved. Please, try again.'));
            }
        }
        $projects = $this->Procedures->Projects->find('list', ['limit' => 200]);
        $this->set(compact('procedure', 'projects'));
        $this->set('_serialize', ['procedure']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Procedure id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $procedure = $this->Procedures->get($id);
        if ($this->Procedures->delete($procedure)) {
            $this->Flash->success(__('The procedure has been deleted.'));
        } else {
            $this->Flash->error(__('The procedure could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
