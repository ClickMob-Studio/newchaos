<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}
if (isset($_GET['claim_king']) && $_GET['claim_king'] === 'claimnow') {
  // Check city doesn't have one already
  $king_query = "SELECT id FROM grpgusers WHERE king = :current_city LIMIT 1";
  $db->query($king_query);
  $db->bind(':current_city', $current_city);
  $king_result = $db->fetch_row();
  if (count($king_result) > 0) {
      // Do nothing - already claimed
  } else {
      if ($user_class->gender === 'Male') {
          $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city AND id = :user_id LIMIT 1";
          $db->query($queen_query);
          $db->bind(':current_city', $current_city);
          $db->bind(':user_id', $user_class->id);
          $queen_result = $db->fetch_row();
          if (count($queen_result) > 0) {
              echo Message("You are already the queen!");
          } else {
              $update_query = "UPDATE grpgusers SET king = :current_city, queen = 0 WHERE id = :user_id";
              $db->query($update_query);
              $db->bind(':current_city', $current_city);
              $db->bind(':user_id', $user_class->id);
              $db->execute();
              header('Location: city.php');
              exit(); // Always exit after a header redirect
          }
      }
  }
}

// Queen city claim
if (isset($_GET['claim_queen']) && $_GET['claim_queen'] === 'claimnow') {
  // Check city doesn't have one already
  $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city LIMIT 1";
  $db->query($queen_query);
  $db->bind(':current_city', $current_city);
  $queen_result = $db->fetch_row();
  if (count($queen_result) > 0) {
      // Do nothing - already claimed
  } else {
      if ($user_class->gender === 'Female') {
          $king_query = "SELECT id FROM grpgusers WHERE king = :current_city AND id = :user_id LIMIT 1";
          $db->query($king_query);
          $db->bind(':current_city', $current_city);
          $db->bind(':user_id', $user_class->id);
          $king_result = $db->fetch_row();
          if (count($king_result) > 0) {
              echo Message("You are already the king!");
          } else {
              $update_query = "UPDATE grpgusers SET queen = :current_city, king = 0 WHERE id = :user_id";
              $db->query($update_query);
              $db->bind(':current_city', $current_city);
              $db->bind(':user_id', $user_class->id);
              $db->execute();
              header('Location: city.php');
              exit(); // Always exit after a header redirect
          }
      }
  }
}
