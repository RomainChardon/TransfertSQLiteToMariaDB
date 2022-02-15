
<form action="" method="get">
    <input type="submit" value="Clear Groupe MYSQL" name="clearGroupeMysql">
</form>

<form action="" method="get">
    <input type="submit" value="Clear User MYSQL" name="clearUserMysql">
</form>

<form action="" method="get">
    <input type="submit" value="Clear Vacance MYSQL" name="clearVacanceMysql">
</form>

<form action="" method="get">
    <input type="submit" value="Transfere Groupe" name="transfereGroupe">
</form>

<form action="" method="get">
    <input type="submit" value="Transfere User" name="transfereUser">
</form>

<form action="" method="get">
    <input type="submit" value="Transfere Vacance" name="transfereVacance">
</form>

<?php


try
{
    $bddSqlite = new PDO('sqlite:data.db');
    echo "BDD SqLite OK ! <br>";
}
catch (Exception $e)
{
    echo "BDD Sqlite Not OK !! <br>";
    die('Erreur : ' . $e->getMessage());
}


try
{
    $bddMysql = new PDO('mysql:host=localhost;dbname=CalendrierVacance;charset=utf8', 'root', 'root');
    echo "BDD MySql OK ! <br>";
}
catch (Exception $e)
{
    echo "BDD MySql Not OK !! <br>";
    die('Erreur : ' . $e->getMessage());
}


// Clear BDD Mysql

if (isset($_GET['clearGroupeMysql'])) {
    $reqGMysql = $bddMysql->prepare('SELECT id FROM groupe');
    $reqGMysql->execute();

    $repGMysql = $reqGMysql->fetchAll();

    foreach ($repGMysql as $suppr) {
        $reqSuppr = $bddMysql->prepare('DELETE FROM groupe WHERE id = :id');
        $reqSuppr->execute([
            'id' => $suppr['id'],
        ]);
    }

    header("Location: ./index.php");
    die();
}


if (isset($_GET['clearUserMysql'])) {
    $reqUMysql = $bddMysql->prepare('SELECT id FROM user');
    $reqUMysql->execute();

    $repUMysql = $reqUMysql->fetchAll();

    foreach ($repUMysql as $suppr) {
        $reqSuppr = $bddMysql->prepare('DELETE FROM user WHERE id = :id');
        $reqSuppr->execute([
            'id' => $suppr['id'],
        ]);
    }

    header("Location: ./index.php");
    die();
}


if (isset($_GET['clearVacanceMysql'])) {
    $reqVMysql = $bddMysql->prepare('SELECT id FROM vacances');
    $reqVMysql->execute();

    $repVMysql = $reqVMysql->fetchAll();

    foreach ($repVMysql as $suppr) {
        $reqSuppr = $bddMysql->prepare('DELETE FROM vacances WHERE id = :id');
        $reqSuppr->execute([
            'id' => $suppr['id'],
        ]);
    }

    header("Location: ./index.php");
    die();
}

// Transfere

if (isset($_GET['transfereGroupe'])) {
    $reqGSqlite = $bddSqlite->prepare('SELECT * FROM groupe');
    $reqGSqlite->execute();

    $repGSqlite = $reqGSqlite->fetchAll();

    foreach ($repGSqlite as $transfere) {
        
        $id = (int)$transfere['id'];
        $nom = $transfere['nom_groupe'];
        $couleur = $transfere['couleur'];
        $transfere = $bddMysql->prepare("INSERT INTO groupe (id, nom_groupe, couleur) VALUES (:id, :nom, :couleur)");
        $transfere->execute([
            'id' => $id,
            'nom' => $nom,
            'couleur' => $couleur,
        ]);
    }

    header('Location: ./index.php');
    die();
}


if (isset($_GET['transfereUser'])) {
    $reqUSqlite = $bddSqlite->prepare('SELECT * FROM user');
    $reqUSqlite->execute();

    $repUSqlite = $reqUSqlite->fetchAll();

    foreach ($repUSqlite as $transfere) {
        $id = (int)$transfere['id'];
        $groupeId = (int)$transfere['groupe_id'];
        $username = $transfere['username'];
        $role = $transfere['roles'];
        $pass = $transfere['password'];
        $nom = $transfere['nom'];
        $prenom = $transfere['prenom'];
        $nbConges = null;
        $mail = strtolower(substr($prenom,0,1).$nom.'@adeo-informatique.com');
        $desa = 0;
        $cadre = 0;
        
        try {
            $transfere = $bddMysql->prepare("INSERT INTO user (id, groupe_id, username, roles, password, nom, prenom, nb_conges, mail, desactiver, cadre) 
            VALUES (:id, :groupeId, :username, :roles, :pass, :nom, :prenom, :nbConges, :mail, :desa, :cadre)");
            $transfere->execute([
                'id' => $id,
                'groupeId' => $groupeId,
                'username' => $username,
                'roles' => $role,
                'pass' => $pass,
                'nom' => $nom,
                'prenom' => $prenom,
                'nbConges' => $nbConges,
                'mail' => $mail,
                'desa' => $desa,
                'cadre' => $cadre,
            ]);
        }
        catch(Exception $e)
        {
                die('Erreur : '.$e->getMessage());
        }
    }

    header('Location: ./index.php');
    die();
}


if (isset($_GET['transfereVacance'])) {
    $reqVSqlite = $bddSqlite->prepare('SELECT * FROM vacances');
    $reqVSqlite->execute();

    $repVSqlite = $reqVSqlite->fetchAll();

    foreach ($repVSqlite as $transfere) {
        try {
            $userV = $bddSqlite->prepare('SELECT user_id FROM user_vacances WHERE vacances_id = :vid');
            $userV->execute([
                'vid' => $transfere['id'],
            ]);
            $rep = $userV->fetchAll();
        }
        catch(Exception $e)
        {
                die('Erreur : '.$e->getMessage());
        }

        $id = (int)$transfere['id'];
        $userId = (int)$rep[0]['user_id'];
        $dateDebut = $transfere['date_debut'];
        $dateFin = $transfere['date_debut'];
        $autoriser = $transfere['autoriser'];
        $attente = $transfere['attente'];
        $maladie = null;
        $demiJournee = null;
        $sansSoldes = null;
        $rtt = null;
        $annuler = null;
        $dateDemande = null;
        $dateAnnulation = null;
        $textAnnuler = null;
        
        try {
            $insert = $bddMysql->prepare("INSERT INTO vacances (id, user_id, date_debut, date_fin, autoriser, attente, maladie, demi_journee, sans_soldes, rtt, annuler, date_demande, date_annulation, text_annuler) 
            VALUES (:id, :userid, :date_debut, :date_fin, :autoriser, :attente, :maladie, :demi_journee, :sans_soldes, :rtt, :annuler, :date_demande, :date_annulation, :text_annuler)");                
                    
            $insert->execute([
                'id' => $id,
                'userid' => $userId,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'autoriser' => $autoriser,
                'attente' => $attente,
                'maladie' => $maladie,
                'demi_journee' => $demiJournee,
                'sans_soldes' => $sansSoldes,
                'rtt' => $rtt,
                'annuler' => $annuler,
                'date_demande' => $dateDemande,
                'date_annulation' => $dateAnnulation,
                'text_annuler' => $textAnnuler,
            ]);     

            
        }
        catch(Exception $e)
        {
                die('Erreur : '.$e->getMessage());
        }
    }

    header('Location: ./index.php');
    die();
}




?>

