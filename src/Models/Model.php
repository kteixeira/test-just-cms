<?php

namespace TestJustCms\Models;
use TestJustCms\Configs\Connection;

class Model extends Connection
{
    public function findAll($column)
    {
        $command = "SELECT id, title FROM ". $column;
        $query = Connection::prepare($command);
        $query->execute();

        return $query->fetchAll();
	}

    public function find($column, $id)
    {
        $command = "SELECT id, title FROM ". $column . " WHERE id = " . $id;
        $query = Connection::prepare($command);
        $query->execute();

        return $query->fetchAll();
    }

    public function create($column, $items)
    {
        $command = "INSERT INTO ". $column ."(title, body, path, created_at, updated_at) 
                    VALUES (:title, :body, :path, :created_at, :upated_at)";

        $query = Connection::prepare($command);

        foreach ($items as $key => $item)
            $query->bindValue($key,  $item->value);

        return $query->execute();
    }

    public function update($column, $items)
    {
        $command = "UPDATE ". $column ."
                    SET title = :title, body = :body, path = :path, updated_at=:updated_at 
                    WHERE id = :id ";

        $query = Connection::prepare($command);

        foreach ($items as $key => $item)
            $query->bindValue($key,  $item->value);

        return $query->execute();
    }

    public function delete($column, $id)
    {
        $command =  "DELETE FROM ". $column ." WHERE id = :id";

        $query = Connection::prepare($command);
        $query->bindValue('id', $id);

        return $query->execute();
    }
}