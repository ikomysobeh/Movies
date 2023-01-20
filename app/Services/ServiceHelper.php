<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ServiceHelper
{
    protected $model;
    protected $attributes;
    protected $searchBy;
    protected $orderBy = 'created_at';
    protected $isAscending = true;

    private function resolveCriteria($data = [], $whereNot = [], $whereNotNull = [])
    {
//        $this->checkAttributes($data);
//        $this->checkAttributes($whereNot);
//        $this->checkAttributes($whereNotNull);
        $query = $this->model->Query();
        $query = $query->select("*");

        if ($this->attributes != null) {
            if (in_array('isVisible', $this->attributes)) {
                $query = $query->where('isVisible', 1);
            }

            foreach ($this->attributes as $val) {
                if (array_key_exists($val, $data)) {
                    $query = $query->where($val, $data[$val]);
                }
                if (array_key_exists($val, $whereNot)) {
                    $query = $query->where($val, '<>', $whereNot[$val]);
                }
                if (in_array($val, $whereNotNull)) {
                    $query = $query->orwhere($val, '<>', '')->whereNotNull($val);
                }
            }
        }


        if (array_key_exists('keyword', $data) && $this->searchBy != null) {
            foreach ($this->searchBy as $k => $q) {
                if ($k == 0) {
                    $query = $query->where($q, 'LIKE', "%" . $data['keyword'] . "%");
                } else {
                    $query = $query->orwhere($q, 'LIKE', "%" . $data['keyword'] . "%");
                }
            }
        }

        $query = $query->orderBy($this->orderBy, $this->isAscending ? 'ASC' : 'DESC');

        if (array_key_exists('limit', $data) && array_key_exists('offset', $data)) {
            $query = $query->take($data['limit']);
            $query = $query->skip($data['offset']);
        }
        return $query;
    }


    private function mapDataModel($data = [])
    {
        foreach ($this->attributes as $val) {
            if (array_key_exists($val, $data)) {
                $this->model->$val = $data[$val];
            }
        }
    }

    public function getOne($id, $slug = '')
    {
        $res = $this->model->findOrFail($id);
        return $res;
    }

    public function getFirst($criteria = [], $whereNot = [], $whereNotNull = [])
    {
        return $this->getList($criteria, $whereNot, $whereNotNull)->first();
    }

    private function checkAttributes($data)
    {
        foreach ($data as $d => $v) {
            if (!in_array($d, $this->attributes)) {
                throw new \Exception($d . " is not in attributes", 500);
            }
        }
    }

    function getList($criteria = [], $whereNot = [], $whereNotNull = [])
    {
        $res = $this->resolveCriteria($criteria, $whereNot, $whereNotNull)->get();
        return $res;
    }

    public function getListQuery($criteria = [], $whereNot = [], $whereNotNull = [])
    {
        $res = $this->resolveCriteria($criteria, $whereNot, $whereNotNull);
        return $res;
    }

    public function saveFile($key, $path, $file)
    {
        if (array_key_exists($key, $file)) {
            return $file[$key]->store($path, 'public');
        }
        return null;
    }

    public function save($data = [])
    {
        $result = false;
        try {
            $this->mapDataModel($data);
            $this->model->save();
            $result = true;
        } catch (\Exception $e) {
        }
        return $result;
    }
    public function update($dataToUpdate = [], $where = [], $whereNot = [], $whereNotNull = [])
    {
        $result = false;
        try {
            $res = $this->resolveCriteria($where, $whereNot, $whereNotNull);
            $res->update($dataToUpdate);
            $result = true;
        } catch (\Exception $e) {
        }
        return $result;
    }

    public function delete($where = [], $whereNot = [], $whereNotNull = [])
    {
        $result = false;
        try {
            $res = $this->resolveCriteria($where, $whereNot, $whereNotNull);
            $res->delete();
            $result = true;
        } catch (\Exception $e) {
        }
        return $result;
    }


}
