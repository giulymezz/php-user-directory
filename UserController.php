<?php

require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/ImageHelper.php';

class UserController extends UserService {  
    public function showUserAction() {

        $active = isset($_POST['active']) && $_POST['active'] !== '' ? (int)$_POST['active'] : null;
        $fromStr = !empty($_POST['from']) ? $_POST['from'] : null;
        $toStr = !empty($_POST['to']) ? $_POST['to'] : null;
        $name = !empty($_POST['name']) ? $_POST['name'] : null;
        $surname = !empty($_POST['surname']) ? $_POST['surname'] : null;
        $view = !empty($_POST['view']) ? $_POST['view'] : 'table';
        
        $sort = null;
        $dir = 'asc';

        $warnings = [];

        $users = $this->getAllUsers();

        $users = $this->filterByActive($users, $active);
        $users = $this->filterByDateRange($users, $fromStr, $toStr);
        $users = $this->filterByNameSurname($users, $name, $surname);

        if ($view === 'table') {
            $sort = $_GET['sort'] ?? null;
            $dir = $_GET['dir'] ?? 'asc';
            $users = $this->sortUsers($users, $sort, $dir, $warnings);
        }

        if (empty($users)) {
            $warnings[] = "The search produced no results";
        }
        
        $data = [
            'users' => $users,
            'sort' => $sort,
            'dir' => $dir,
            'active' => $active,
            'from' => $fromStr,
            'to' => $toStr,
            'name' => $name,
            'surname' => $surname,
            'warnings' => $warnings,
        ];

        if ($view === 'thumb') {
            require __DIR__ . '/views/thumb.php';
        } else {
            require __DIR__ . '/views/table.php';
        }
    }

    protected function filterByActive(array $users, ?int $active): array {
        if ($active === null) {
            return $users;
        }

        return array_filter($users, function ($u) use ($active) {
            return (int)$u->active === $active;
        });
    }

    /**
     * Supported formats:
     * - d/m/Y
     * - d/m/Y H:i
     * - d/m/Y H:i:s
     */
    protected function filterByDateRange(array $users, ?string $fromStr, ?string $toStr): array {
        if (!$fromStr && !$toStr) {
            return $users;
        }

        $from = $this->parseFlexibleDate($fromStr);
        $to   = $this->parseFlexibleDate($toStr);
        
        if ($fromStr && !$from) {
            return [];
        }
        
        if ($toStr && !$to) {
            return [];
        }

        return array_filter($users, function ($u) use ($from, $to) {
            $login = new DateTime($u->last_login);

            if ($from && $login < $from) {
                return false;
            }

            if ($to && $login > $to) {
                return false;
            }

            return true;
        });
    }

    private function parseFlexibleDate(?string $str): DateTime|false|null {

        if (!$str) {
            return null;
        }
        
        $parts = explode(" ", $str);

        if (count($parts) === 1) {
            [$d, $m, $y] = array_pad(explode("/", $parts[0]), 3, null);

            if (!checkdate((int)$m, (int)$d, (int)$y)) {
                return false;
            }

            return DateTime::createFromFormat('d/m/Y H:i:s', "$str 00:00:00");
        }

        if (count($parts) === 2) {

            [$date, $time] = $parts;
            [$d, $m, $y] = explode("/", $date);

            if (!checkdate((int)$m, (int)$d, (int)$y)) {
                return false;
            }

            $timeParts = explode(":", $time);

            if (count($timeParts) === 2) {
                [$h, $i] = $timeParts;
                $s = 0;
            } else if (count($timeParts) === 3) {
                [$h, $i, $s] = $timeParts;
            } else {
                return false;
            }

            $dt = DateTime::createFromFormat('d/m/Y H:i:s', "$date $h:$i:$s");

            return $dt ?: false;
        }

        return false;
    }

    protected function filterByNameSurname(array $users, ?string $name, ?string $surname): array {
        if (!$name && !$surname) {
            return $users;
        }

        $name = $name ? mb_strtolower($name) : null;
        $surname = $surname ? mb_strtolower($surname) : null;

        return array_filter($users, function ($u) use ($name, $surname) {
            $un = mb_strtolower($u->name);
            $us = mb_strtolower($u->surname);

            $okName = $name ? (mb_strpos($un, $name) === 0) : true;
            $okSurname = $surname ? (mb_strpos($us, $surname) === 0) : true;

            return $okName && $okSurname;
        });
    }
    
    protected function sortUsers(array $users, ?string $sort, string $dir, array &$warnings = []): array {
        if (!$sort) {
            return $users;
        }

        $allowedSort = ['id', 'name', 'surname', 'active', 'last_login', 'rating'];
        if (!in_array($sort, $allowedSort, true)) {
            $warnings[] = "Parametro di ordinamento non valido: $sort";
            return $users;
        }

        $dir = strtolower($dir) === 'desc' ? 'desc' : 'asc';

        usort($users, function ($a, $b) use ($sort, $dir) {
            $va = $a->$sort ?? null;
            $vb = $b->$sort ?? null;

            if ($sort === 'last_login') {
                $va = new \DateTime($va);
                $vb = new \DateTime($vb);
            }

            if ($va == $vb) {
                return 0;
            }

            if ($dir === 'asc') {
                return ($va < $vb) ? -1 : 1;
            }
            return ($va > $vb) ? -1 : 1;
        });

        return $users;
    }
}   