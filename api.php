<?php

    header("Content-Type:application/json");

    $dsn = 'mysql:host=localhost;dbname=pdt_vitrine';
    $username = 'root';
    $password = '';

    //Mettez à jour ce qui précède avec vos identifiants de base de données.

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
            $stmt->bindValue(':img', $img, PDO::PARAM_STR);
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
        default:
            $req = "SELECT *
                        FROM products
                        WHERE statut > 0";

            $stmt = $pdo->query($req);
            $tab = $stmt->fetchAll(PDO::FETCH_OBJ);
            // print_r($tab);
    }
