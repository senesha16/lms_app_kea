<?php

class database {

    function opencon() {
        return new PDO("
        mysql:host=localhost;
        dbname=lms_app", 
        "root", 
        "");
    }

    function signUpUser($firstname, $lastname, $birthday, $sex, $email, $phone, $username, $password, $profile_picture_path) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO Users (user_FN, user_LN, user_birthday, user_sex, user_email, user_phone, user_username, user_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $birthday, $sex, $email, $phone, $username, $password]);

            $userId = $con->lastInsertId();

            $stmt = $con->prepare("INSERT INTO users_pictures (user_id, user_pic_url) VALUES (?, ?)");
            $stmt->execute([$userId, $profile_picture_path]);

            $con->commit();
            return $userId;
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

    function addAuthor($authorFirstName, $authorLastName, $authorBirthYear, $authorNationality) {
          $con = $this->opencon();

           try {
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO Authors (author_FN, author_LN, author_birthday, author_nat) VALUES (?, ?, ?, ?)");
            $stmt->execute([$authorFirstName, $authorLastName, $authorBirthYear, $authorNationality]);

            $author_id = $con->lastinsertId();

            $con->commit();
            return $author_id;
           } catch (PDOException $e) {
            $con->rollBack();
            return false;
           }
    }

    function addGenre($genreName) {
          $con = $this->opencon();

           try {
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO genres (genre_Name) VALUES (?)");
            $stmt->execute([$genreName]);

            $genre_id = $con->lastinsertId();

            $con->commit();
            return $genre_id;
           } catch (PDOException $e) {
            $con->rollBack();
            return false;
           }
    }

    function insertAddress($userId, $street, $barangay, $city, $province) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO Address (ba_street, ba_barangay, ba_city, ba_province) VALUES (?, ?, ?, ?)");
            $stmt->execute([$street, $barangay, $city, $province]);

            $addressId = $con->lastInsertId();

            $stmt = $con->prepare("INSERT INTO Users_Address (user_id, address_id) VALUES (?, ?)");
            $stmt->execute([$userId, $addressId]);

            $con->commit();
            return true;
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

    function loginUser($email, $password){
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM Users WHERE user_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if ($user && password_verify($password, $user['user_password'])) {
            return $user;
        } else {
            return false;
        }
    }



}
?>
