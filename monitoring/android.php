<?php
$server = "localhost";
$server_username = "falconpl_main";
$server_password = "falcon123!";
$database_name = "falconpl_main";
$conn = new Mysqli($server, $server_username, $server_password, $database_name);

$api = $_GET['api'];
if ( ! in_array($api, array('upt', 'ultg', 'penghantar', 'section', 'pegawai', 'tower', 'sutt', 'jarak_objek', 'pentanahan', 'historikal_gangguan', 'tindak_lanjut', 'riwayat_penebangan'))) {
  $response = ['status' => false, 'message' => 'missing api'];
  echo json_encode($response);
  die();
}

$table = $api;

if ($api == 'jarak_objek') { $table = 'kerawanan'; }
if ($api == 'pentanahan') { $table = 'pertanahan'; }

if ($api == 'upt') {
  $sql = "SELECT * FROM upt";
} else {
  $id_upt = $_GET['id_upt'];
  $query = $conn->query("SELECT * FROM upt WHERE id_upt = ".$id_upt);
  while ($data = $query->fetch_assoc()) {
    $data_upt[] = $data;
  }
  if (sizeof($data_upt) != 1) {
    $response = ['status' => false, 'message' => 'missing api'];
    echo json_encode($response);
    die();
  } else {
    $sql = "SELECT * FROM ".$table." WHERE id_upt = ".$id_upt;
  }
}

$query = $conn->query($sql);
$response_data = null;
$i = 0;
while ($data = $query->fetch_assoc()) {
  $response_data[$i] = $data;
  if ($table == 'tower') {
    $response_data[$i]['jarak_objek'] = $data['jarak_pohon'];
    $response_data[$i]['pentanahan'] = $data['pertanahan'];
  }
  if ($table == 'kerawanan') {
    $response_data[$i]['id_jarak_objek'] = $data['id_kerawanan'];
  }
  if ($table == 'pertanahan') {
    $response_data[$i]['id_pentanahan'] = $data['id_pertanahan'];
    $response_data[$i]['leg_a'] = $data['leg_A'];
    $response_data[$i]['leg_b'] = $data['leg_B'];
    $response_data[$i]['leg_c'] = $data['leg_C'];
    $response_data[$i]['leg_d'] = $data['leg_D'];
  }
  $i++;
}
if (is_null($response_data)) {
  $status = false;
} else {
  $status = true;
}
header('Content-Type: application/json');
$response = ['status' => $status, $api => $response_data];
echo json_encode($response);
