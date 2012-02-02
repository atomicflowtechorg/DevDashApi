<?php

class ListModel extends CI_Model {

    var $id = '';
    var $name = '';
    var $type = '';
    var $access = '';
    var $owner = '';

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function createList() {
        $this->name = $this->input->post('name');
        $this->type = $this->input->post('type');
        $this->access = $this->input->post('access');

        $dbTable = '';

        if ($this->type == 'Team') {
            $this->owner = $this->input->post('owner');
        } else {
            $session = $this->session->all_userdata();
            $this->owner = $session['username'];
        }

        $this->db->query("INSERT INTO tblList (fldListName,fldType,fldAccessLevel,fldOwner) VALUES ('$this->name' , '$this->type' , '$this->access','$this->owner')");
    }

    function getOwner($listId) {

        $query = $this->db->query("SELECT fldType FROM tblList WHERE pkListId=$listId");
        $result = $query->row();
        $type = $result->fldType;
        if ($type == 'Personal') {
            $query = $this->db->query("SELECT fkUsername FROM tblListTasker WHERE fkListId=$listId");
            $result = $query->row();
            $owner = $result->fkUsername;
        } else {
            $query = $this->db->query("SELECT fkTeamName FROM tblListTeam WHERE fkListId=$listId");
            $result = $query->row();
            $owner = $result->fkTeamName;
        }

        return $owner;
    }

    function delete($listId) {
        $query = $this->db->query("UPDATE tblList SET fldActive=0 WHERE pkListId=$listId");
    }

    function getAllLists($searchTerm = null) {
        if ($searchTerm === null) {
            $query = $this->db->query("SELECT pkListId,fldListName,fldType,fldOwner,fldAccessLevel,fldActive FROM tblList");
            return $query->result();
        } else {
            $this->load->library('SearchResult');
            $results = array();
            $query = $this->db->query("SELECT pkListId,fldListName,fldType,fldOwner,fldAccessLevel,fldActive 
                    FROM tblList 
                    WHERE fldListName 
                    LIKE '%$searchTerm%' 
                    OR fldOwner LIKE '%$searchTerm%'");

            foreach ($query->result() as $row) {
                $resultObject = new SearchResult;
                $resultObject->type = "list";
                $resultObject->link = site_url("owner/" . $row->fldOwner . "/list/" . $row->pkListId."/");
                $resultObject->title = $row->fldListName;
                array_push($results, $resultObject);
            }
            return $results;
        }
    }

    function getListData($listId) {
        $query = $this->db->query("SELECT pkListId,fldListName,fldType,fldOwner,fldAccessLevel,fldActive FROM tblList WHERE pkListId=$listId");
        return $query->result();
    }

}