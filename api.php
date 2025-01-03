<?php

    header("Content-Type:application/json");

    $dsn = 'mysql:host=localhost;dbname=pdt_vitrine';
    $username = 'root';
    $password = '';

try {
        $pdo = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
    }

    if (!isset($_GET["action"])){

        $req = "SELECT *
                        FROM products
                        WHERE statut > 0";

            $stmt = $pdo->query($req);
            $tab = $stmt->fetchAll(PDO::FETCH_OBJ);
            // print_r($tab);
            echo json_encode($tab);
            exit;
    }
    switch ($_GET["action"]) {

        case 'login':
            $email = $_POST["email"];
            $mdp = $_POST["mdp"];

            $req = "SELECT COUNT(*) AS NB
                    FROM  utilisateurs 
                    WHERE email = :email AND mdp = :mdp";
            $stmt = $pdo->prepare($req);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':mdp', $mdp, PDO::PARAM_STR);
            $stmt->execute();
            $tab = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tab['NB'] > 0) {
                $json = [
                    'action' => 'login',
                    'statut' => 'ok',
                    'NB' => $tab['NB']
                ];
            } else {
                $json = [
                    'action' => 'login',
                    'statut' => 'error',
                    'NB' => 0 
                ];
            }
            echo json_encode($json);
            break;

        case 'delete':
            $Id = $_GET["id"];

            $req = "UPDATE products 
                            SET statut=0 
                            WHERE id = :id";

            $stmt = $pdo->prepare($req);
            $stmt->bindValue(':id', $Id, PDO::PARAM_INT);
            $stmt->execute();
            $json = [
                'action' => 'delete',
                'statut' => 'ok',
                'message' => 'Supprimé avec succès'
            ];
            echo json_encode($json);
            break;
        case 'ajout':
            $nomPdt = $_POST["designation"];
            $prix = $_POST["prix"];
            //$img = $_POST["img"];
            $description = $_POST["description"];

            $req = "INSERT INTO products (designation, prix, img, description, statut) 
                        VALUES (:designation, :prix, :img, :description, 1)";
            $stmt = $pdo->prepare($req);
            $stmt->bindValue(':designation', $nomPdt, PDO::PARAM_STR);
            $stmt->bindValue(':prix', $prix, PDO::PARAM_STR);
            $stmt->bindValue(':img',null);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
            $json = [
                'action' => 'insert',
                'statut' => 'ok',
                'message' => 'Ajouté avec succès',
                'id' => $pdo->lastInsertId()
            ];
            echo json_encode($json);
            break;  
         case 'modifie':
            $id = $_POST["id"];
            $nomPdt = $_POST["designation"];
            $prix = $_POST["prix"];
           // $img = $_POST["img"];
            $description = $_POST["description"];

            if (!isset($id) || empty($nomPdt) || empty($prix) || empty($description)) {
                $json = [
                    'action' => 'modifier',
                    'statut' => 'error',
                    'message' => 'Tous les champs sont nécessaires'
                ];
                //echo json_encode($json);
                exit;
            }

            $req = "UPDATE products 
                    SET designation = :designation, prix = :prix, img = :img, description = :description 
                    WHERE id = :id AND statut > 0";
            $stmt = $pdo->prepare($req);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':designation', $nomPdt, PDO::PARAM_STR);
            $stmt->bindValue(':prix', $prix, PDO::PARAM_STR);
            $stmt->bindValue(':img', null);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $json = [
                    'action' => 'modifier',
                    'statut' => 'ok',
                    'message' => 'Modifié avec succès',
                ];
            } else {
                $json = [
                    'action' => 'modifier',
                    'statut' => 'error',
                    'message' => 'Erreur dans la modification'
                ];
            }
            echo json_encode($json);
            break;
        default:
            $req = "SELECT *
                        FROM products
                        WHERE statut > 0";

            $stmt = $pdo->query($req);
            $tab = $stmt->fetchAll(PDO::FETCH_OBJ);
            // print_r($tab);
    }
