<?php

namespace App\Repositories;

use App\Models\NursesNote;

class NursesNotesRepositories
{

    public function list($filter = [])
    {
        $note = NursesNote::with(['user'])->orderBy('id', 'desc');
        $note = $note->get();
        return $note->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $note = NursesNote::where('pid', $pid)->first();

            if (!$note) {
                return [];
            }

            return $note;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {

            $note = NursesNote::create($data);
            return $note;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($note_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $note = NursesNote::findOrFail($note_id);
            $note->update($data);

            return $note;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
