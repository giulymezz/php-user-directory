<?php

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
        $users = $this->filterByDateRange($users, $fromStr, $toStr, $warnings);
        $users = $this->filterByNameSurname($users, $name, $surname);

        if ($view === 'table') {
            $sort = $_GET['sort'] ?? null;
            $dir = $_GET['dir'] ?? 'asc';
            $users = $this->sortUsers($users, $sort, $dir, $warnings);
        }

        foreach ($users as $user) {
            $user->picture = $this->resizeImageTo100($user->picture);
        }
        
        $data = [
            'users' => $users,
            'sort' => $sort,
            'dir' => $dir,
            'warnings' => $warnings,
        ];

        if ($view === 'thumb') {
            require __DIR__ . '/views/thumb.php';
        } else {
            require __DIR__ . '/views/table.php';
        }
    }


    /* =======================
       Metodi di filtro
       ======================= */

    protected function filterByActive(array $users, ?int $active): array {
        if ($active === null) {
            return $users;
        }

        return array_filter($users, function ($u) use ($active) {
            return (int)$u->active === $active;
        });
    }

    protected function filterByDateRange(array $users, ?string $fromStr, ?string $toStr, array &$warnings = []): array {
        if (!$fromStr && !$toStr) {
            return $users;
        }

        $from = $fromStr ? \DateTime::createFromFormat('d/m/Y H:i:s', $fromStr) : null;
        $to = $toStr ? \DateTime::createFromFormat('d/m/Y H:i:s', $toStr) : null;
        
        if ($fromStr && !$from) {
            $warnings[] = "La data 'From' non è valida. Nessun filtro applicato.";
            return $users;
        }
        
        if ($toStr && !$to) {
            $warnings[] = "La data 'To' non è valida. Nessun filtro applicato.";
            return $users;
        }

        return array_filter($users, function ($u) use ($from, $to) {
            $login = new \DateTime($u->last_login);

            if ($from && $login < $from) {
                return false;
            }
            if ($to && $login > $to) {
                return false;
            }
            return true;
        });
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


    /* =======================
       Sorting per la tabella
       ======================= */
    
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


    /* =======================
       Resize immagini a 100px
       ======================= */

    protected function resizeImageTo100(string $srcPath): string {
        $fullSrc = __DIR__ . '/' . $srcPath;

        if (!file_exists($fullSrc)) {
            return $srcPath;
        }

        $cacheDir = __DIR__ . '/data/cache';

        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0777, true) && !is_dir($cacheDir)) {
                throw new \RuntimeException("Impossibile creare la cartella cache: $cacheDir");
            }
        }

        if (!is_writable($cacheDir)) {
            throw new \RuntimeException("La cartella cache non è scrivibile: $cacheDir");
        }

        $filename = basename($srcPath);
        $destPath = $cacheDir . '/' . $filename;

        if (file_exists($destPath)) {
            return 'data/cache/' . $filename;
        }

        $srcImg = imagecreatefromjpeg($fullSrc);
 
        $origW = imagesx($srcImg);   
        $origH = imagesy($srcImg);

        $newW = 100;   
        $ratio = $origH / $origW;
        $newH = (int)round($newW * $ratio);

        $dstImg = imagecreatetruecolor($newW, $newH);

        imagecopyresampled(
            $dstImg, $srcImg,
            0, 0, 0, 0,
            $newW, $newH,
            $origW, $origH
        );
        
        imagejpeg($dstImg, $destPath, 90);

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        return 'data/cache/' . $filename;
    }
}   
