<?php
// ARQUIVO: api.php (VERSÃO FINAL E VERIFICADA)
header('Content-Type: application/json; charset=utf-8');
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$inputJSON = json_decode(file_get_contents('php://input'), true);
$inputPOST = $_POST;
$files = $_FILES;

// --- ROTEADOR PRINCIPAL DE AÇÕES ---
switch ($action) {
    // Pessoas
    case 'getPessoas': getPessoas($conn); break;
    case 'getPessoaById': getPessoaById($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addPessoa': if ($method == 'POST') addPessoa($conn, $inputPOST, $files); break;
    case 'updatePessoa': if ($method == 'POST') updatePessoa($conn, $inputPOST, $files); break;
    case 'deletePessoa': if ($method == 'POST') deletePessoa($conn, $inputJSON); break;
    case 'getOrganizacoes': getOrganizacoes($conn); break;
    
    // Casos
    case 'getCasos': getCasos($conn); break;
    case 'getCasoDetails': getCasoDetails($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addCaso': if ($method == 'POST') addCaso($conn, $inputJSON); break;
    case 'updateCaso': if ($method == 'POST') updateCaso($conn, $inputJSON); break;
    case 'deleteCaso': if ($method == 'POST') deleteCaso($conn, $inputJSON); break;
    
    // Ocorrências
    case 'getOcorrencias': getOcorrencias($conn); break;
    case 'getOcorrenciaDetails': getOcorrenciaDetails($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addOcorrencia': if ($method == 'POST') addOcorrencia($conn, $inputJSON); break;
    case 'updateOcorrencia': if ($method == 'POST') updateOcorrencia($conn, $inputJSON); break;
    case 'deleteOcorrencia': if ($method == 'POST') deleteOcorrencia($conn, $inputJSON); break;

    // Veículos
    case 'getVeiculos': getVeiculos($conn); break;
    case 'getVeiculoById': getVeiculoById($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addVeiculo': if ($method == 'POST') addVeiculo($conn, $inputJSON); break;
    case 'updateVeiculo': if ($method == 'POST') updateVeiculo($conn, $inputJSON); break;
    case 'deleteVeiculo': if ($method == 'POST') deleteVeiculo($conn, $inputJSON); break;

    // Objetos
    case 'getObjetos': getObjetos($conn); break;
    case 'getObjetoById': getObjetoById($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addObjeto': if ($method == 'POST') addObjeto($conn, $inputJSON); break;
    case 'updateObjeto': if ($method == 'POST') updateObjeto($conn, $inputJSON); break;
    case 'deleteObjeto': if ($method == 'POST') deleteObjeto($conn, $inputJSON); break;

    // Telefones
    case 'getTelefones': getTelefones($conn); break;
    case 'getTelefoneById': getTelefoneById($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addTelefone': if ($method == 'POST') addTelefone($conn, $inputJSON); break;
    case 'updateTelefone': if ($method == 'POST') updateTelefone($conn, $inputJSON); break;
    case 'deleteTelefone': if ($method == 'POST') deleteTelefone($conn, $inputJSON); break;

    // Locais
    case 'getLocais': getLocais($conn); break;
    case 'getLocalById': getLocalById($conn, (int)($_GET['id'] ?? 0)); break;
    case 'addLocal': if ($method == 'POST') addLocal($conn, $inputJSON); break;
    case 'updateLocal': if ($method == 'POST') updateLocal($conn, $inputJSON); break;
    case 'deleteLocal': if ($method == 'POST') deleteLocal($conn, $inputJSON); break;
    
    // Buscas e Análise
    case 'searchPessoas': searchPessoas($conn, $_GET['term'] ?? ''); break;
    case 'searchCasos': searchCasos($conn, $_GET['term'] ?? ''); break;
    case 'searchLocais': searchLocais($conn, $_GET['term'] ?? ''); break;
    case 'searchOcorrencias': searchOcorrencias($conn, $_GET['term'] ?? ''); break;
    case 'searchVeiculos': searchVeiculos($conn, $_GET['term'] ?? ''); break;
    case 'searchObjetos': searchObjetos($conn, $_GET['term'] ?? ''); break;
    case 'searchTelefones': searchTelefones($conn, $_GET['term'] ?? ''); break;
    case 'getGraphData': getGraphData($conn, (int)($_GET['pessoa_id'] ?? 0)); break;
    case 'getGraphDataForCase': getGraphDataForCase($conn, (int)($_GET['caso_id'] ?? 0)); break;
    case 'addVinculoManual': if ($method == 'POST') addVinculoManual($conn, $inputJSON); break; 
    
    default:
        echo json_encode(['success' => false, 'message' => 'Ação desconhecida.']);
        break;
}

$conn->close();

// --- FUNÇÕES DE BUSCA ---
function searchPessoas($conn, $term)
{
    $s = "%" . $term . "%";
    $stmt = $conn->prepare("SELECT id, nome_completo, cpf FROM pessoas WHERE nome_completo LIKE ? OR cpf LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function searchOcorrencias($conn, $term)
{
    $s = "%" . $term . "%";
    $stmt = $conn->prepare("SELECT id, numero_bo, fatos_comunicados FROM ocorrencias WHERE numero_bo LIKE ? OR fatos_comunicados LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function searchVeiculos($conn, $term)
{
    $s = "%" . $term . "%";
    $stmt = $conn->prepare("SELECT id, placa, marca_modelo FROM veiculos WHERE placa LIKE ? OR marca_modelo LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function searchObjetos($conn, $term)
{
    $s = "%" . $term . "%";
    $stmt = $conn->prepare("SELECT id, tipo, marca FROM objetos WHERE tipo LIKE ? OR marca LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function searchTelefones($conn, $term)
{
    $s = "%" . $term . "%";
    $stmt = $conn->prepare("SELECT id, numero FROM telefones WHERE numero LIKE ? OR imei LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function searchCasos($conn, $term)
{
    $s = "%" . $term . "%";
    $stmt = $conn->prepare("SELECT id, inquerito_policial FROM casos WHERE inquerito_policial LIKE ? OR CAST(id AS CHAR) LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function searchLocais($conn, $term)
{
    $s = "%" . $term . "%";
    // Busca pela descrição, rua ou município
    $stmt = $conn->prepare("SELECT id, descricao, rua, municipio, uf FROM locais WHERE descricao LIKE ? OR rua LIKE ? OR municipio LIKE ? LIMIT 5");
    $stmt->bind_param("sss", $s, $s, $s);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}
function getOrganizacoes($conn)
{
    $result = $conn->query("SELECT id, nome FROM organizacoes_criminosas ORDER BY nome ASC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

// --- FUNÇÕES CRUD PESSOAS ---
function getPessoas($conn)
{
    $r = $conn->query("SELECT id, nome_completo, alcunha, cpf FROM pessoas ORDER BY id DESC");
    echo json_encode($r->fetch_all(MYSQLI_ASSOC));
}
function getPessoaById($conn, $id)
{
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.', 'data' => null]);
        return;
    }
    $response = ['success' => true, 'message' => '', 'data' => ['pessoa' => null, 'afiliacoes' => [], 'tatuagens' => []]];

    try {
        // Get main person data
        $stmt_p = $conn->prepare("SELECT * FROM pessoas WHERE id = ?");
        if ($stmt_p) {
            $stmt_p->bind_param("i", $id);
            if ($stmt_p->execute()) {
                $result_p = $stmt_p->get_result();
                if ($result_p) {
                    $response['data']['pessoa'] = $result_p->fetch_assoc();
                }
            }
            $stmt_p->close();
        }

        if ($response['data']['pessoa']) {
            // Get affiliations only if person was found
            $stmt_o = $conn->prepare("SELECT organizacao_id FROM pessoa_organizacao WHERE pessoa_id = ?");
            if ($stmt_o) {
                $stmt_o->bind_param("i", $id);
                if ($stmt_o->execute()) {
                    $result_o = $stmt_o->get_result();
                    if ($result_o) {
                        while ($row = $result_o->fetch_assoc()) {
                            $response['data']['afiliacoes'][] = (int) $row['organizacao_id']; // Cast to int
                        }
                    }
                }
                $stmt_o->close();
            }

            // Get tattoos only if person was found
            $stmt_t = $conn->prepare("SELECT local_corpo, descricao FROM tatuagens WHERE pessoa_id = ?");
            if ($stmt_t) {
                $stmt_t->bind_param("i", $id);
                if ($stmt_t->execute()) {
                    $result_t = $stmt_t->get_result();
                    if ($result_t) {
                        $response['data']['tatuagens'] = $result_t->fetch_all(MYSQLI_ASSOC);
                    }
                }
                $stmt_t->close();
            }
        }
    } catch (mysqli_sql_exception $e) {
        error_log("SQL Exception in getPessoaById: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Erro no banco de dados ao buscar pessoa.', 'data' => null];
    } catch (Exception $e) {
        error_log("General Exception in getPessoaById: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Erro geral ao buscar pessoa.', 'data' => null];
    }

    echo json_encode($response);
}

//
// >>> EM api.php, SUBSTITUA AS FUNÇÕES addPessoa e updatePessoa POR ESTAS <<<
//

function addPessoa($conn, $input, $files)
{
    $foto_path = null;
    if (isset($files['foto']) && $files['foto']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '-' . basename($files['foto']['name']);
        $foto_path = $upload_dir . $file_name;
        if (!move_uploaded_file($files['foto']['tmp_name'], $foto_path)) {
            echo json_encode(['success' => false, 'message' => 'Falha ao mover arquivo.']);
            return;
        }
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare(
            "INSERT INTO pessoas (nome_completo, alcunha, cpf, rg, sexo, data_nascimento, nome_pai, nome_mae, foto_path, naturalidade, nacionalidade, cor_cabelo, cor_olhos, cor_pele, faixa_etaria, historico_delitos, sentencas, periodos_reclusao, atuacao_geografica, redes_sociais) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        // Atribuição segura dos campos que podem não existir no formulário
        $nome_completo = $input['nome_completo'] ?? '';
        $alcunha = $input['alcunha'] ?? null;
        $cpf = $input['cpf'] ?? null;
        $rg = $input['rg'] ?? null;
        $sexo = $input['sexo'] ?? null;
        $data_nascimento = !empty($input['data_nascimento']) ? $input['data_nascimento'] : null;
        $nome_pai = $input['nome_pai'] ?? null;
        $nome_mae = $input['nome_mae'] ?? null;
        $naturalidade = $input['naturalidade'] ?? null;
        $nacionalidade = $input['nacionalidade'] ?? null;
        $cor_cabelo = $input['cor_cabelo'] ?? null;
        $cor_olhos = $input['cor_olhos'] ?? null;
        $cor_pele = $input['cor_pele'] ?? null;
        $faixa_etaria = $input['faixa_etaria'] ?? null;
        $historico_delitos = $input['historico_delitos'] ?? null;
        $sentencas = $input['sentencas'] ?? null;
        $periodos_reclusao = $input['periodos_reclusao'] ?? null;
        $atuacao_geografica = $input['atuacao_geografica'] ?? null;
        $redes_sociais = $input['redes_sociais'] ?? null;

        $stmt->bind_param(
            "ssssssssssssssssssss",
            $nome_completo,
            $alcunha,
            $cpf,
            $rg,
            $sexo,
            $data_nascimento,
            $nome_pai,
            $nome_mae,
            $foto_path,
            $naturalidade,
            $nacionalidade,
            $cor_cabelo,
            $cor_olhos,
            $cor_pele,
            $faixa_etaria,
            $historico_delitos,
            $sentencas,
            $periodos_reclusao,
            $atuacao_geografica,
            $redes_sociais
        );

        $stmt->execute();
        $pessoa_id = $conn->insert_id;
        if (!$pessoa_id)
            throw new Exception("Falha ao criar pessoa.");

        if (!empty($input['afiliacoes'])) {
            $afiliacoes_ids = explode(',', $input['afiliacoes']);
            $stmt_org = $conn->prepare("INSERT INTO pessoa_organizacao (pessoa_id, organizacao_id) VALUES (?, ?)");
            foreach ($afiliacoes_ids as $org_id) {
                if (filter_var($org_id, FILTER_VALIDATE_INT)) {
                    $stmt_org->bind_param("ii", $pessoa_id, $org_id);
                    $stmt_org->execute();
                }
            }
            $stmt_org->close();
        }

        if (!empty($input['tatuagens'])) {
            $tatuagens = json_decode($input['tatuagens'], true);
            if (is_array($tatuagens)) {
                $stmt_tattoo = $conn->prepare("INSERT INTO tatuagens (pessoa_id, local_corpo, descricao) VALUES (?, ?, ?)");
                foreach ($tatuagens as $tattoo) {
                    if (isset($tattoo['local_corpo']) && isset($tattoo['descricao'])) {
                        $stmt_tattoo->bind_param("iss", $pessoa_id, $tattoo['local_corpo'], $tattoo['descricao']);
                        $stmt_tattoo->execute();
                    }
                }
                $stmt_tattoo->close();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

//
// >>> EM api.php, SUBSTITUA A FUNÇÃO updatePessoa PELA VERSÃO ABAIXO <<<
//
function updatePessoa($conn, $input, $files) { 
    $id = (int)$input['id']; 
    if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID inválido.']); return; }

    $foto_path = $input['foto_existente'] ?? null; 
    if (isset($files['foto']) && $files['foto']['error'] == 0) {
        $upload_dir='uploads/'; 
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); } 
        $file_name = uniqid().'-'.basename($files['foto']['name']); 
        $foto_path = $upload_dir.$file_name; 
        if (isset($input['foto_existente']) && !empty($input['foto_existente']) && file_exists($input['foto_existente'])) {
            unlink($input['foto_existente']);
        }
        if (!move_uploaded_file($files['foto']['tmp_name'], $foto_path)) { 
            echo json_encode(['success'=>false, 'message'=>'Falha ao mover arquivo.']); return; 
        }
    } 
    
    $conn->begin_transaction(); 
    try { 
        $stmt = $conn->prepare(
            "UPDATE pessoas SET 
             nome_completo=?, alcunha=?, cpf=?, rg=?, sexo=?, data_nascimento=?, nome_pai=?, nome_mae=?, foto_path=?,
             naturalidade=?, nacionalidade=?, cor_cabelo=?, cor_olhos=?, cor_pele=?, faixa_etaria=?, 
             historico_delitos=?, sentencas=?, periodos_reclusao=?, atuacao_geografica=?, redes_sociais=?
             WHERE id=?"
        );
        
        // Atribuição segura de todos os campos
        $nome_completo = $input['nome_completo'] ?? ''; $alcunha = $input['alcunha'] ?? null;
        $cpf = $input['cpf'] ?? null; $rg = $input['rg'] ?? null; $sexo = $input['sexo'] ?? null;
        $data_nascimento = !empty($input['data_nascimento']) ? $input['data_nascimento'] : null;
        $nome_pai = $input['nome_pai'] ?? null; $nome_mae = $input['nome_mae'] ?? null;
        $naturalidade = $input['naturalidade'] ?? null; $nacionalidade = $input['nacionalidade'] ?? null;
        $cor_cabelo = $input['cor_cabelo'] ?? null; $cor_olhos = $input['cor_olhos'] ?? null;
        $cor_pele = $input['cor_pele'] ?? null; // O campo está aqui
        $faixa_etaria = $input['faixa_etaria'] ?? null;
        $historico_delitos = $input['historico_delitos'] ?? null; $sentencas = $input['sentencas'] ?? null;
        $periodos_reclusao = $input['periodos_reclusao'] ?? null; $atuacao_geografica = $input['atuacao_geografica'] ?? null;
        $redes_sociais = $input['redes_sociais'] ?? null;

        $stmt->bind_param("ssssssssssssssssssssi", 
            $nome_completo, $alcunha, $cpf, $rg, $sexo, $data_nascimento, $nome_pai, $nome_mae, $foto_path,
            $naturalidade, $nacionalidade, $cor_cabelo, $cor_olhos, $cor_pele, $faixa_etaria, 
            $historico_delitos, $sentencas, $periodos_reclusao, $atuacao_geografica, $redes_sociais,
            $id
        ); 
        $stmt->execute(); 
        $stmt->close(); 
        
        // Lógica de Afiliações e Tatuagens
        $stmt_del_org = $conn->prepare("DELETE FROM pessoa_organizacao WHERE pessoa_id = ?");
        $stmt_del_org->bind_param("i", $id); $stmt_del_org->execute(); $stmt_del_org->close();
        if (!empty($input['afiliacoes'])) { 
            $afiliacoes_ids = explode(',', $input['afiliacoes']); 
            $stmt_org = $conn->prepare("INSERT INTO pessoa_organizacao (pessoa_id, organizacao_id) VALUES (?, ?)"); 
            foreach($afiliacoes_ids as $org_id) { 
                 if (filter_var(trim($org_id), FILTER_VALIDATE_INT)) {
                    $stmt_org->bind_param("ii", $id, $org_id); 
                    $stmt_org->execute(); 
                 }
            } 
            $stmt_org->close(); 
        } 
        
        $stmt_del_tattoo = $conn->prepare("DELETE FROM tatuagens WHERE pessoa_id = ?");
        $stmt_del_tattoo->bind_param("i", $id); $stmt_del_tattoo->execute(); $stmt_del_tattoo->close();
        if (!empty($input['tatuagens'])) { 
            $tatuagens = json_decode($input['tatuagens'], true); 
            if (is_array($tatuagens)) { 
                $stmt_tattoo = $conn->prepare("INSERT INTO tatuagens (pessoa_id, local_corpo, descricao) VALUES (?, ?, ?)"); 
                foreach($tatuagens as $tattoo) { 
                    if (isset($tattoo['local_corpo']) && isset($tattoo['descricao'])) {
                        $stmt_tattoo->bind_param("iss", $id, $tattoo['local_corpo'], $tattoo['descricao']); 
                        $stmt_tattoo->execute(); 
                    }
                } 
                $stmt_tattoo->close(); 
            } 
        } 
        
        $conn->commit(); 
        echo json_encode(['success' => true]); 
    } catch (Exception $e) { 
        $conn->rollback(); 
        echo json_encode(['success' => false, 'message' => $e->getMessage()]); 
    } 
}
function deletePessoa($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("DELETE FROM pessoas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}

// --- FUNÇÕES CRUD LOCAIS ---

function getLocais($conn)
{
    // A consulta agora seleciona TODAS as colunas (*) da tabela.
    $result = $conn->query("SELECT * FROM locais ORDER BY id DESC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function getLocalById($conn, $id)
{
    $id = (int) $id;
    $stmt = $conn->prepare("SELECT * FROM locais WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
    $stmt->close();
}

function addLocal($conn, $input)
{
    // Opcional: Implementação de busca por CEP com a API ViaCEP
    // Se um CEP for enviado, tenta buscar os dados de endereço
    if (!empty($input['cep'])) {
        $cep = preg_replace("/[^0-9]/", "", $input['cep']);
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $response = @file_get_contents($url);
        if ($response) {
            $data = json_decode($response, true);
            if (!isset($data['erro'])) {
                // Sobrescreve os campos do input com os dados do ViaCEP se estiverem vazios
                $input['rua'] = empty($input['rua']) ? ($data['logradouro'] ?? '') : $input['rua'];
                $input['bairro'] = empty($input['bairro']) ? ($data['bairro'] ?? '') : $input['bairro'];
                $input['municipio'] = empty($input['municipio']) ? ($data['localidade'] ?? '') : $input['municipio'];
                $input['uf'] = empty($input['uf']) ? ($data['uf'] ?? '') : $input['uf'];
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO locais (descricao, rua, numero, bairro, municipio, uf, cep, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Converte latitude e longitude para null se estiverem vazios
    $lat = !empty($input['latitude']) ? $input['latitude'] : null;
    $lon = !empty($input['longitude']) ? $input['longitude'] : null;

    $stmt->bind_param("sssssssss", $input['descricao'], $input['rua'], $input['numero'], $input['bairro'], $input['municipio'], $input['uf'], $input['cep'], $lat, $lon);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}

function updateLocal($conn, $input)
{
    $id = (int) $input['id'];
    $lat = !empty($input['latitude']) ? (float) $input['latitude'] : null;
    $lon = !empty($input['longitude']) ? (float) $input['longitude'] : null;

    $stmt = $conn->prepare("UPDATE locais SET descricao=?, rua=?, numero=?, bairro=?, municipio=?, uf=?, cep=?, latitude=?, longitude=? WHERE id=?");

    // CORREÇÃO: Os tipos para latitude e longitude foram trocados de 's' (string) para 'd' (double/decimal)
    $stmt->bind_param("sssssssddi", $input['descricao'], $input['rua'], $input['numero'], $input['bairro'], $input['municipio'], $input['uf'], $input['cep'], $lat, $lon, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}

function deleteLocal($conn, $input)
{
    $id = (int) $input['id'];
    // CUIDADO: Adicionar verificação se o local está vinculado a alguma ocorrência/caso antes de excluir.
    // Por enquanto, a exclusão é direta.
    $stmt = $conn->prepare("DELETE FROM locais WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}

// --- FUNÇÕES DE CRUD (OUTRAS) ---
// (O restante das funções de Ocorrências, Casos, Veículos, etc., seguem aqui)
function getOcorrencias($conn)
{
    $sql = "SELECT o.id, o.numero_bo, o.data_fato, o.fatos_comunicados, l.municipio, l.uf, COUNT(op.pessoa_id) AS total_envolvidos FROM ocorrencias o LEFT JOIN locais l ON o.local_id = l.id LEFT JOIN ocorrencia_pessoa op ON o.id = op.ocorrencia_id GROUP BY o.id ORDER BY o.data_fato DESC";
    echo json_encode($conn->query($sql)->fetch_all(MYSQLI_ASSOC));
}
function getOcorrenciaDetails($conn, $id)
{ // $id já é (int) vindo do roteador
    if ($id <= 0) {
        echo json_encode(['ocorrencia' => null, 'envolvidos' => [], 'message' => 'ID de ocorrência inválido.']);
        return;
    }

    $response = ['ocorrencia' => null, 'envolvidos' => []];

    // Usar LEFT JOIN para garantir que a ocorrência seja retornada mesmo se o local não existir
    $stmt_main = $conn->prepare(
        "SELECT o.*, 
                l.rua, l.numero, l.bairro, l.municipio, l.uf, l.cep 
         FROM ocorrencias o 
         LEFT JOIN locais l ON o.local_id = l.id 
         WHERE o.id = ?"
    );
    $stmt_main->bind_param("i", $id);
    $stmt_main->execute();
    $ocorrencia_data = $stmt_main->get_result()->fetch_assoc();
    $stmt_main->close();

    if (!$ocorrencia_data) {
        echo json_encode(['ocorrencia' => null, 'envolvidos' => [], 'message' => 'Ocorrência não encontrada.']);
        return;
    }
    $response['ocorrencia'] = $ocorrencia_data;

    // Buscar envolvidos
    $stmt_envolvidos = $conn->prepare(
        "SELECT p.id, p.nome_completo, op.participacao 
         FROM ocorrencia_pessoa op 
         JOIN pessoas p ON op.pessoa_id = p.id 
         WHERE op.ocorrencia_id = ?"
    );
    $stmt_envolvidos->bind_param("i", $id);
    $stmt_envolvidos->execute();
    $response['envolvidos'] = $stmt_envolvidos->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_envolvidos->close();

    echo json_encode($response);
}

// VERSÃO NOVA E SIMPLIFICADA
function addOcorrencia($conn, $input)
{
    // Validação básica
    if (empty($input['local_id']) || empty($input['numero_bo'])) {
        echo json_encode(['success' => false, 'message' => 'Dados essenciais faltando: Nº do BO e Local são obrigatórios.']);
        return;
    }

    $conn->begin_transaction();
    try {
        // A função não insere mais em 'locais', apenas usa o local_id recebido.
        $stmt_ocorrencia = $conn->prepare("INSERT INTO ocorrencias (numero_bo, data_fato, local_id, fatos_comunicados) VALUES (?, ?, ?, ?)");
        $stmt_ocorrencia->bind_param("ssis", $input['numero_bo'], $input['data_fato'], $input['local_id'], $input['fatos_comunicados']);
        $stmt_ocorrencia->execute();
        $ocorrencia_id = $conn->insert_id;

        if (!$ocorrencia_id) {
            throw new Exception("Falha ao registrar ocorrência.");
        }

        if (!empty($input['envolvidos'])) {
            $stmt_vinculo = $conn->prepare("INSERT INTO ocorrencia_pessoa (ocorrencia_id, pessoa_id, participacao) VALUES (?, ?, ?)");
            foreach ($input['envolvidos'] as $envolvido) {
                if (empty($envolvido['id']))
                    continue;
                $stmt_vinculo->bind_param("iis", $ocorrencia_id, $envolvido['id'], $envolvido['participacao']);
                $stmt_vinculo->execute();
            }
            $stmt_vinculo->close();
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
function updateOcorrencia($conn, $input)
{
    $id = (int) $input['id'];
    $conn->begin_transaction();
    try {
        $stmt_get_local = $conn->prepare("SELECT local_id FROM ocorrencias WHERE id = ?");
        $stmt_get_local->bind_param("i", $id);
        $stmt_get_local->execute();
        $local_id = $stmt_get_local->get_result()->fetch_assoc()['local_id'];
        if (!$local_id)
            throw new Exception("Local não encontrado.");
        $stmt_local = $conn->prepare("UPDATE locais SET rua=?, numero=?, bairro=?, municipio=?, uf=?, cep=? WHERE id=?");
        $stmt_local->bind_param("ssssssi", $input['rua'], $input['numero'], $input['bairro'], $input['municipio'], $input['uf'], $input['cep'], $local_id);
        $stmt_local->execute();
        $stmt_ocorrencia = $conn->prepare("UPDATE ocorrencias SET numero_bo=?, data_fato=?, fatos_comunicados=? WHERE id=?");
        $stmt_ocorrencia->bind_param("sssi", $input['numero_bo'], $input['data_fato'], $input['fatos_comunicados'], $id);
        $stmt_ocorrencia->execute();
        $stmt_del_v = $conn->prepare("DELETE FROM ocorrencia_pessoa WHERE ocorrencia_id = ?");
        $stmt_del_v->bind_param("i", $id);
        $stmt_del_v->execute();
        if (!empty($input['envolvidos'])) {
            $stmt_v = $conn->prepare("INSERT INTO ocorrencia_pessoa (ocorrencia_id, pessoa_id, participacao) VALUES (?, ?, ?)");
            foreach ($input['envolvidos'] as $e) {
                if (empty($e['id']))
                    continue;
                $stmt_v->bind_param("iis", $id, $e['id'], $e['participacao']);
                $stmt_v->execute();
            }
            $stmt_v->close();
        }
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar ocorrência: ' . $e->getMessage()]);
    }
}
function deleteOcorrencia($conn, $input)
{
    $id = (int) $input['id'];
    $conn->begin_transaction();
    try {
        $stmt_v = $conn->prepare("DELETE FROM ocorrencia_pessoa WHERE ocorrencia_id = ?");
        $stmt_v->bind_param("i", $id);
        $stmt_v->execute();
        $stmt_o = $conn->prepare("DELETE FROM ocorrencias WHERE id = ?");
        $stmt_o->bind_param("i", $id);
        $stmt_o->execute();
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
function getCasos($conn)
{
    $sql = "SELECT c.id,c.inquerito_policial,c.data_criacao, (SELECT COUNT(*) FROM caso_ocorrencia WHERE caso_id=c.id) as total_ocorrencias, (SELECT COUNT(*) FROM caso_pessoa WHERE caso_id=c.id) as total_pessoas, (SELECT COUNT(*) FROM caso_veiculo WHERE caso_id=c.id) as total_veiculos, (SELECT COUNT(*) FROM caso_objeto WHERE caso_id=c.id) as total_objetos, (SELECT COUNT(*) FROM caso_telefone WHERE caso_id=c.id) as total_telefones FROM casos c ORDER BY c.id DESC";
    echo json_encode($conn->query($sql)->fetch_all(MYSQLI_ASSOC));
}
function getCasoDetails($conn, $id)
{
    $id = (int) $id;
    $response = [];
    $stmt_main = $conn->prepare("SELECT * FROM casos WHERE id=?");
    $stmt_main->bind_param("i", $id);
    $stmt_main->execute();
    $response['caso'] = $stmt_main->get_result()->fetch_assoc();
    if (!$response['caso']) {
        echo json_encode(null);
        return;
    }
    $tables = ['ocorrencia' => 'SELECT o.id, o.numero_bo, o.fatos_comunicados FROM caso_ocorrencia co JOIN ocorrencias o ON co.ocorrencia_id=o.id WHERE co.caso_id=?', 'pessoa' => 'SELECT p.id, p.nome_completo, cp.atuacao FROM caso_pessoa cp JOIN pessoas p ON cp.pessoa_id=p.id WHERE cp.caso_id=?', 'veiculo' => 'SELECT v.id, v.placa, v.marca_modelo FROM caso_veiculo cv JOIN veiculos v ON cv.veiculo_id=v.id WHERE cv.caso_id=?', 'objeto' => 'SELECT o.id, o.tipo, o.marca FROM caso_objeto co JOIN objetos o ON co.objeto_id=o.id WHERE co.caso_id=?', 'telefone' => 'SELECT t.id, t.numero FROM caso_telefone ct JOIN telefones t ON ct.telefone_id=t.id WHERE ct.caso_id=?'];
    foreach ($tables as $table_name => $sql) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response[$table_name . 's'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    echo json_encode($response);
}
// EM api.php, SUBSTITUA as funções addCaso e updateCaso

// EM api.php, SUBSTITUA as funções addCaso e updateCaso

function addCaso($conn, $input) { 
    $conn->begin_transaction(); 
    try { 
        // CORREÇÃO: Adicionados os campos investigacoes e conclusao
        $stmt_caso = $conn->prepare("INSERT INTO casos (inquerito_policial, autos, relato_fatos, investigacoes, conclusao) VALUES (?, ?, ?, ?, ?)");
        $stmt_caso->bind_param("sssss", $input['inquerito_policial'], $input['autos'], $input['relato_fatos'], $input['investigacoes'], $input['conclusao']);
        $stmt_caso->execute(); 
        $caso_id = $conn->insert_id; 
        if(!$caso_id) throw new Exception("Falha ao criar caso.");
        
        // O restante da lógica para vincular entidades permanece o mesmo
        foreach (['ocorrencia', 'pessoa', 'veiculo', 'objeto', 'telefone'] as $table) {
            $key = $table . 's';
            if (!empty($input[$key])) {
                $col = $table . '_id';
                $sql = "INSERT INTO caso_{$table} (caso_id, {$col}" . ($table === 'pessoa' ? ', atuacao' : '') . ") VALUES (?,?" . ($table === 'pessoa' ? ',?' : '') . ")";
                $stmt = $conn->prepare($sql);
                foreach ($input[$key] as $item) {
                    $item_id = ($table === 'pessoa') ? $item['id'] : $item;
                    if (empty($item_id)) continue;
                    if ($table === 'pessoa') {
                        $stmt->bind_param("iis", $caso_id, $item_id, $item['atuacao']);
                    } else {
                        $stmt->bind_param("ii", $caso_id, $item_id);
                    }
                    $stmt->execute();
                }
                $stmt->close();
            }
        }
        $conn->commit();
        echo json_encode(['success'=>true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
    }
}

function updateCaso($conn, $input) {
    $id=(int)$input['id']; 
    $conn->begin_transaction(); 
    try { 
        // CORREÇÃO: Adicionados os campos investigacoes e conclusao
        $stmt_caso = $conn->prepare("UPDATE casos SET inquerito_policial=?, autos=?, relato_fatos=?, investigacoes=?, conclusao=? WHERE id=?");
        $stmt_caso->bind_param("sssssi", $input['inquerito_policial'], $input['autos'], $input['relato_fatos'], $input['investigacoes'], $input['conclusao'], $id);
        $stmt_caso->execute(); 
        $stmt_caso->close();

        // O restante da lógica para atualizar vínculos permanece o mesmo
        foreach (['caso_ocorrencia', 'caso_pessoa', 'caso_veiculo', 'caso_objeto', 'caso_telefone'] as $table_to_delete) {
            $stmt_delete = $conn->prepare("DELETE FROM {$table_to_delete} WHERE caso_id = ?");
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();
            $stmt_delete->close();
        }
        foreach (['ocorrencia', 'pessoa', 'veiculo', 'objeto', 'telefone'] as $table) {
            $key = $table . 's';
            if (!empty($input[$key])) {
                $col = $table . '_id';
                $sql = "INSERT INTO caso_{$table} (caso_id, {$col}" . ($table === 'pessoa' ? ', atuacao' : '') . ") VALUES (?,?" . ($table === 'pessoa' ? ',?' : '') . ")";
                $stmt = $conn->prepare($sql);
                foreach ($input[$key] as $item) {
                    $item_id = ($table === 'pessoa') ? $item['id'] : $item;
                    if (empty($item_id)) continue;
                    if ($table === 'pessoa') {
                        $stmt->bind_param("iis", $id, $item_id, $item['atuacao']);
                    } else {
                        $stmt->bind_param("ii", $id, $item_id);
                    }
                    $stmt->execute();
                }
                $stmt->close();
            }
        }
        $conn->commit();
        echo json_encode(['success'=>true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
    }
}
function deleteCaso($conn, $input)
{
    $id = (int) $input['id'];
    $conn->begin_transaction();
    try {
        foreach (['caso_ocorrencia', 'caso_pessoa', 'caso_veiculo', 'caso_objeto', 'caso_telefone'] as $tbl) {
            $stmt_del = $conn->prepare("DELETE FROM {$tbl} WHERE caso_id = ?");
            $stmt_del->bind_param("i", $id);
            $stmt_del->execute();
            $stmt_del->close();
        }
        $stmt_caso = $conn->prepare("DELETE FROM casos WHERE id = ?");
        $stmt_caso->bind_param("i", $id);
        $stmt_caso->execute();
        $stmt_caso->close();
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
function getVeiculos($conn)
{
    $result = $conn->query("SELECT * FROM veiculos ORDER BY id DESC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}
function getVeiculoById($conn, $id)
{
    $id = (int) $id;
    $stmt = $conn->prepare("SELECT * FROM veiculos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
    $stmt->close();
}
function addVeiculo($conn, $input)
{
    $stmt = $conn->prepare("INSERT INTO veiculos (placa, marca_modelo, ano_modelo, cor, combustivel, renavam, chassi) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $input['placa'], $input['marca_modelo'], $input['ano_modelo'], $input['cor'], $input['combustivel'], $input['renavam'], $input['chassi']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function updateVeiculo($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("UPDATE veiculos SET placa=?, marca_modelo=?, ano_modelo=?, cor=?, combustivel=?, renavam=?, chassi=? WHERE id=?");
    $stmt->bind_param("sssssssi", $input['placa'], $input['marca_modelo'], $input['ano_modelo'], $input['cor'], $input['combustivel'], $input['renavam'], $input['chassi'], $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function deleteVeiculo($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("DELETE FROM veiculos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function getObjetos($conn)
{
    $result = $conn->query("SELECT * FROM objetos ORDER BY id DESC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}
function getObjetoById($conn, $id)
{
    $id = (int) $id;
    $stmt = $conn->prepare("SELECT * FROM objetos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
    $stmt->close();
}
function addObjeto($conn, $input)
{
    $stmt = $conn->prepare("INSERT INTO objetos (tipo, marca, modelo, numero_serie, quantidade, observacoes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $input['tipo'], $input['marca'], $input['modelo'], $input['numero_serie'], $input['quantidade'], $input['observacoes']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function updateObjeto($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("UPDATE objetos SET tipo=?, marca=?, modelo=?, numero_serie=?, quantidade=?, observacoes=? WHERE id=?");
    $stmt->bind_param("ssssisi", $input['tipo'], $input['marca'], $input['modelo'], $input['numero_serie'], $input['quantidade'], $input['observacoes'], $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function deleteObjeto($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("DELETE FROM objetos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function getTelefones($conn)
{
    $result = $conn->query("SELECT * FROM telefones ORDER BY id DESC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}
function getTelefoneById($conn, $id)
{
    $id = (int) $id;
    $stmt = $conn->prepare("SELECT * FROM telefones WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
    $stmt->close();
}
function addTelefone($conn, $input)
{
    $stmt = $conn->prepare("INSERT INTO telefones (numero, imei, operadora) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $input['numero'], $input['imei'], $input['operadora']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function updateTelefone($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("UPDATE telefones SET numero=?, imei=?, operadora=? WHERE id=?");
    $stmt->bind_param("sssi", $input['numero'], $input['imei'], $input['operadora'], $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function deleteTelefone($conn, $input)
{
    $id = (int) $input['id'];
    $stmt = $conn->prepare("DELETE FROM telefones WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
function getGraphData($conn, $root_pessoa_id)
{
    $root_pessoa_id = (int) $root_pessoa_id;
    if ($root_pessoa_id <= 0) {
        echo json_encode(['nodes' => [], 'edges' => []]);
        return;
    }

    $nodes = [];
    $edges = [];

    // 1. Adiciona o nó raiz (a pessoa pesquisada)
    $stmt_root = $conn->prepare("SELECT id, nome_completo FROM pessoas WHERE id = ?");
    $stmt_root->bind_param("i", $root_pessoa_id);
    $stmt_root->execute();
    if ($root_pessoa = $stmt_root->get_result()->fetch_assoc()) {
        $nodes['pessoa_' . $root_pessoa['id']] = ['id' => 'pessoa_' . $root_pessoa['id'], 'label' => $root_pessoa['nome_completo'], 'group' => 'pessoa', 'size' => 30];
    }
    $stmt_root->close();

    // 2. Busca vínculos através de OCORRÊNCIAS (lógica existente)
    $ocorrencia_ids = [];
    $stmt_ocorrencias = $conn->prepare("SELECT DISTINCT ocorrencia_id FROM ocorrencia_pessoa WHERE pessoa_id = ?");
    $stmt_ocorrencias->bind_param("i", $root_pessoa_id);
    $stmt_ocorrencias->execute();
    $result_ocorrencias = $stmt_ocorrencias->get_result();
    while ($row = $result_ocorrencias->fetch_assoc()) {
        $ocorrencia_ids[] = $row['ocorrencia_id'];
    }
    $stmt_ocorrencias->close();

    if (!empty($ocorrencia_ids)) {
        $ocorrencia_ids_str = implode(',', array_map('intval', $ocorrencia_ids));
        $ocorrencias_result = $conn->query("SELECT id, numero_bo FROM ocorrencias WHERE id IN ($ocorrencia_ids_str)");
        $ocorrencias_data = [];
        while ($row = $ocorrencias_result->fetch_assoc()) {
            $occ_id_str = 'occ_' . $row['id'];
            if (!isset($nodes[$occ_id_str])) {
                $nodes[$occ_id_str] = ['id' => $occ_id_str, 'label' => "BO: " . $row['numero_bo'], 'group' => 'ocorrencia'];
            }
            $edges[] = ['from' => 'pessoa_' . $root_pessoa_id, 'to' => $occ_id_str, 'label' => 'Envolvido(a)'];
        }

        $envolvidos_result = $conn->query("SELECT p.id, p.nome_completo, op.ocorrencia_id FROM ocorrencia_pessoa op JOIN pessoas p ON op.pessoa_id = p.id WHERE op.ocorrencia_id IN ($ocorrencia_ids_str) AND op.pessoa_id != $root_pessoa_id");
        while ($row = $envolvidos_result->fetch_assoc()) {
            $pessoa_id_str = 'pessoa_' . $row['id'];
            if (!isset($nodes[$pessoa_id_str])) {
                $nodes[$pessoa_id_str] = ['id' => $pessoa_id_str, 'label' => $row['nome_completo'], 'group' => 'pessoa'];
            }
            $edges[] = ['from' => $pessoa_id_str, 'to' => 'occ_' . $row['ocorrencia_id'], 'label' => 'Envolvido(a)'];
        }
    }

    // 3. (NOVO) Busca por VÍNCULOS MANUAIS diretos
    $stmt_vinculos = $conn->prepare("SELECT * FROM vinculos WHERE (entidade1_tipo = 'pessoa' AND entidade1_id = ?) OR (entidade2_tipo = 'pessoa' AND entidade2_id = ?)");
    $stmt_vinculos->bind_param("ii", $root_pessoa_id, $root_pessoa_id);
    $stmt_vinculos->execute();
    $result_vinculos = $stmt_vinculos->get_result();

    while ($vinculo = $result_vinculos->fetch_assoc()) {
        // Determina qual é a "outra" entidade no vínculo
        $outra_entidade_tipo = ($vinculo['entidade1_id'] == $root_pessoa_id && $vinculo['entidade1_tipo'] == 'pessoa') ? $vinculo['entidade2_tipo'] : $vinculo['entidade1_tipo'];
        $outra_entidade_id = ($vinculo['entidade1_id'] == $root_pessoa_id && $vinculo['entidade1_tipo'] == 'pessoa') ? $vinculo['entidade2_id'] : $vinculo['entidade1_id'];

        $outra_entidade_node_id = $outra_entidade_tipo . '_' . $outra_entidade_id;

        // Se o nó da outra entidade ainda não foi adicionado, busca seus detalhes e o adiciona
        if (!isset($nodes[$outra_entidade_node_id])) {
            $label = "ID: $outra_entidade_id"; // Label padrão
            $table_map = [
                'pessoa' => ['table' => 'pessoas', 'col' => 'nome_completo'],
                'veiculo' => ['table' => 'veiculos', 'col' => 'placa'],
                'objeto' => ['table' => 'objetos', 'col' => 'tipo'],
                'telefone' => ['table' => 'telefones', 'col' => 'numero']
            ];

            if (array_key_exists($outra_entidade_tipo, $table_map)) {
                $table = $table_map[$outra_entidade_tipo]['table'];
                $col = $table_map[$outra_entidade_tipo]['col'];
                $stmt_entidade = $conn->prepare("SELECT $col FROM $table WHERE id = ?");
                $stmt_entidade->bind_param("i", $outra_entidade_id);
                $stmt_entidade->execute();
                if ($res_entidade = $stmt_entidade->get_result()->fetch_assoc()) {
                    $label = $res_entidade[$col];
                }
                $stmt_entidade->close();
            }
            $nodes[$outra_entidade_node_id] = ['id' => $outra_entidade_node_id, 'label' => $label, 'group' => $outra_entidade_tipo];
        }

        // Adiciona a aresta (a linha de ligação)
        $edges[] = [
            'from' => 'pessoa_' . $root_pessoa_id,
            'to' => $outra_entidade_node_id,
            'label' => $vinculo['tipo_vinculo'],
            'dashes' => true, // Linhas tracejadas para diferenciar de vínculos de ocorrência
            'color' => '#2ecc71'
        ];
    }
    $stmt_vinculos->close();

    echo json_encode(['nodes' => array_values($nodes), 'edges' => $edges]);
}
//
// >>> SUBSTITUA A FUNÇÃO getGraphDataForCase ANTIGA PELA VERSÃO COMPLETA ABAIXO <<<
//

function getGraphDataForCase($conn, $caso_id)
{
    $caso_id = (int) $caso_id;
    if ($caso_id <= 0) {
        echo json_encode(['nodes' => [], 'edges' => []]);
        return;
    }

    $nodes = [];
    $edges = [];
    $entities_in_case = []; // Armazenar todas as entidades do caso para buscar vínculos entre elas

    // 1. Adiciona o nó raiz (o Caso)
    $stmt_caso = $conn->prepare("SELECT id, inquerito_policial FROM casos WHERE id = ?");
    $stmt_caso->bind_param("i", $caso_id);
    $stmt_caso->execute();
    if ($caso_data = $stmt_caso->get_result()->fetch_assoc()) {
        $nodes['caso_' . $caso_id] = ['id' => 'caso_' . $caso_id, 'label' => 'Caso IP: ' . $caso_data['inquerito_policial'], 'group' => 'caso', 'size' => 30];
    }
    $stmt_caso->close();

    // 2. Busca todas as entidades diretamente ligadas ao caso
    $linked_tables = [
        'pessoa' => ['sql' => 'SELECT p.id, p.nome_completo as label, j.atuacao FROM caso_pessoa j JOIN pessoas p ON j.pessoa_id = p.id WHERE j.caso_id = ?'],
        'ocorrencia' => ['sql' => 'SELECT o.id, o.numero_bo as label FROM caso_ocorrencia j JOIN ocorrencias o ON j.ocorrencia_id = o.id WHERE j.caso_id = ?', 'prefix' => 'BO: '],
        'veiculo' => ['sql' => 'SELECT v.id, v.placa as label FROM caso_veiculo j JOIN veiculos v ON j.veiculo_id = v.id WHERE j.caso_id = ?', 'prefix' => 'Placa: '],
        'objeto' => ['sql' => 'SELECT o.id, o.tipo as label FROM caso_objeto j JOIN objetos o ON j.objeto_id = o.id WHERE j.caso_id = ?', 'prefix' => 'Obj: '],
        'telefone' => ['sql' => 'SELECT t.id, t.numero as label FROM caso_telefone j JOIN telefones t ON j.telefone_id = t.id WHERE j.caso_id = ?', 'prefix' => 'Tel: ']
    ];

    foreach ($linked_tables as $group => $config) {
        $stmt = $conn->prepare($config['sql']);
        $stmt->bind_param("i", $caso_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $node_id = $group . '_' . $row['id'];
            if (!isset($nodes[$node_id])) {
                $label_prefix = $config['prefix'] ?? '';
                $nodes[$node_id] = ['id' => $node_id, 'label' => $label_prefix . $row['label'], 'group' => $group];
                $entities_in_case[] = ['type' => $group, 'id' => $row['id']]; // Adiciona a entidade à lista para verificação de vínculos
            }
            $edge_label = ($group === 'pessoa') ? $row['atuacao'] : '';
            $edges[] = ['from' => 'caso_' . $caso_id, 'to' => $node_id, 'label' => $edge_label];
        }
        $stmt->close();
    }

    // 3. (NOVO) Busca por vínculos manuais ENTRE as entidades encontradas no caso
    $processed_edges = []; // Para evitar adicionar arestas duplicadas
    foreach ($entities_in_case as $entity) {
        $stmt_vinculos = $conn->prepare("SELECT * FROM vinculos WHERE (entidade1_tipo = ? AND entidade1_id = ?) OR (entidade2_tipo = ? AND entidade2_id = ?)");
        $stmt_vinculos->bind_param("sisi", $entity['type'], $entity['id'], $entity['type'], $entity['id']);
        $stmt_vinculos->execute();
        $result_vinculos = $stmt_vinculos->get_result();

        while ($vinculo = $result_vinculos->fetch_assoc()) {
            $node1_id_str = $vinculo['entidade1_tipo'] . '_' . $vinculo['entidade1_id'];
            $node2_id_str = $vinculo['entidade2_tipo'] . '_' . $vinculo['entidade2_id'];

            // Garante que ambos os nós do vínculo manual existam no grafo do caso atual
            if (isset($nodes[$node1_id_str]) && isset($nodes[$node2_id_str])) {
                // Cria uma chave única para a aresta para evitar duplicidade (ex: pessoa_1-veiculo_2)
                $edge_key_parts = [$node1_id_str, $node2_id_str];
                sort($edge_key_parts);
                $edge_key = implode('-', $edge_key_parts);

                if (!isset($processed_edges[$edge_key])) {
                    $edges[] = [
                        'from' => $node1_id_str,
                        'to' => $node2_id_str,
                        'label' => $vinculo['tipo_vinculo'],
                        'dashes' => true,
                        'color' => '#27ae60' // Cor verde para vínculos manuais
                    ];
                    $processed_edges[$edge_key] = true;
                }
            }
        }
        $stmt_vinculos->close();
    }

    echo json_encode(['nodes' => array_values($nodes), 'edges' => $edges]);
}
function addVinculoManual($conn, $input)
{
    // Validação básica para garantir que todos os campos necessários foram enviados
    if (empty($input['entidade1_tipo']) || empty($input['entidade1_id']) || empty($input['entidade2_tipo']) || empty($input['entidade2_id']) || empty($input['tipo_vinculo'])) {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes para criar o vínculo.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO vinculos (entidade1_tipo, entidade1_id, entidade2_tipo, entidade2_id, tipo_vinculo, intensidade, fonte_info) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // CORREÇÃO APLICADA AQUI: O último 'i' foi trocado por 's'.
    $stmt->bind_param(
        "sisssss",
        $input['entidade1_tipo'],
        $input['entidade1_id'],
        $input['entidade2_tipo'],
        $input['entidade2_id'],
        $input['tipo_vinculo'],
        $input['intensidade'],
        $input['fonte_info']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Vínculo criado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar vínculo: ' . $stmt->error]);
    }
    $stmt->close();
}
?>