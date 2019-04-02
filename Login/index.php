<?php

include "..\PHP\ConectaSQL.php";

if (isset($_GET['ID'])) {
	$ID = !empty($_POST['id']) ? $_POST['id'] : $_GET['ID'];
	$TCC = Conecta()->query("SELECT * FROM trabalho WHERE idtrabalho = '$ID'")->fetch_assoc();
}

$ID = !empty($_POST['id']) ? $_POST['id'] : $TCC['idtrabalho'];
$SENHA = $_POST['senha'];
$AUTORES = !empty($_POST['autores']) ? $_POST['autores'] : $TCC['autores'];
$ORIENTADOR = !empty($_POST['orientador']) ? $_POST['orientador'] : $TCC['orientador'];
$CURSO = !empty($_POST['curso']) ? $_POST['curso'] : $TCC['curso'];
$ANO = !empty($_POST['ano']) ? $_POST['ano'] : $TCC['ano'];
$TITULO = !empty($_POST['titulo']) ? $_POST['titulo'] : $TCC['titulo'];
$CUTTER = !empty($_POST['cutter']) ? $_POST['cutter'] : $TCC['cutter'];
$CLASSIFICACAO = !empty($_POST['classificacao']) ? $_POST['classificacao'] : $TCC['classificacao'];
$PDF = !empty($_FILES['pdf']['name']) ? $_FILES['pdf']['name'] : $TCC['pdf'];

if (isset($_POST['submit'])) {
	if (empty($ID)) {
		$ID = strtoupper(uniqid()); //$MYSQLI->insert_id;
	
	$INSERT = Conecta()->query("INSERT INTO trabalho (idtrabalho, senha, autores, orientador, curso, ano, titulo, cutter, classificacao) VALUES ('$ID', '$SENHA', '$AUTORES', '$ORIENTADOR', '$CURSO', $ANO, '$TITULO', '$CUTTER', '$CLASSIFICACAO')");
		if ($INSERT) {
			echo "TCC cadastrado com sucesso.";
			$ENVIAPDF = true;
		} else {
			echo "Não foi possível cadastra seu TCC.";
		}
	} else {
		$SELECT = Conecta()->query("SELECT * FROM trabalho WHERE idtrabalho = '$ID' and senha = '$SENHA'");
		if ($SELECT->num_rows > 0) {
			$UPDATE = Conecta()->query("UPDATE trabalho SET autores = '$AUTORES', orientador = '$ORIENTADOR', curso = '$CURSO', ano = $ANO, titulo = '$TITULO', cutter  = '$CUTTER', classificacao = '$CLASSIFICACAO', pdf = '$PDF' WHERE idtrabalho = '$ID'");
			if ($UPDATE) {
				echo "TCC atualizado com sucesso.";
				$ENVIAPDF = true;
			} else {
				echo "Não foi possível atualizar seu TCC.";
			}
		} else {
			echo "Senha incorreta.";
		}
	}
	if (!empty($_FILES['pdf']['name']) && $ENVIAPDF) {
		if ($_FILES['pdf']['type'] == 'application/pdf') {
			$DIR = '../PDF/';
			$PDF = $ID.".pdf"; //basename($_FILES['pdf']['name']);
			if (move_uploaded_file($_FILES['pdf']['tmp_name'], $DIR.$PDF)) {
				echo "Arquivo enviado com sucesso.";
			} else {
				echo "Upload do arquivo não foi cncluido. ".$_FILES["pdf"]["error"];
			}
		} else {
			echo "Tipo de Arquivo inválido.";
		}
	}
}

include "..\PHP\ListaCurso.php";

echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='pt'>
	<head>
		<title>Cadastro de Trabalhos Acadêmicos</title>
		<meta http-equiv='Content-Language' content='pt-br' />
		<meta http-equiv='Content-Type' content='text/html' />
		<meta http-equiv='X-UA-Compatible' content='IE=Edge' />
		<!--[if IE]>
		<link rel='icon' href='/Imagens/Favicon.ico' />
		<![endif]-->
		<link rel='icon' href='../Imagens/Favicon.png' />
		<link href='../CSS/Style.css' rel='stylesheet' type='text/css' />
		<script src='../JS/JqueryMin.js'></script>
	</head>
	<body>
		<div class='container'>
			<h1 class='titulo'>Cadastro de Trabalhos Acadêmicos</h1>
			<div class='cantainer-c'>
				<form enctype='multipart/form-data' class='cadastro-form' action='' method='POST'>
					<p>Preencha o formulário</p>
					<label class='cadastro-id-txt".(empty($ID) ? " hidden" : "")."'>Identificação: <input class='cadastro-input' name='id' type='text' value='$ID' disabled='disabled' /></label>
					<label class='cadastro-autores-txt'>Nome dos autores: <input class='cadastro-input' name='autores' type='text' value='$AUTORES' onchange='validaForm(this)' /></label>
					<label class='cadastro-orientador-txt'>Nome do orientador: <input class='cadastro-input' name='orientador' type='text'  value='$ORIENTADOR' onchange='validaForm(this)' /></label>
					<label class='cadastro-curso-txt'>Curso: <select class='cadastro-input' name='curso' type='text' value='$CURSO' onchange='validaForm(this)' value='$IDCURSO'>
						<option value=''>Selecione</option>".ListaCurso($CURSO)."
					<select/></label>
					<label class='cadastro-ano-txt'>Ano: <input class='cadastro-input' name='ano' type='number' min='1980' max='2020' value='$ANO' onchange='validaForm(this)' /></label>
					<label class='cadastro-titulo-txt'>Título: <input class='cadastro-input' name='titulo' type='text' value='$TITULO' onchange='validaForm(this)' /></label>
					<label class='cadastro-classificacao-txt'>Classificação: <input class='cadastro-input' name='classificacao' type='text' value='$CLASSIFICACAO' onchange='validaForm(this)' /></label>
					<label class='cadastro-cutter-txt'>Cutter (...): <input class='cadastro-input' name='cutter' type='text' value='$CUTTER' onchange='validaForm(this)' /></label>
					<label class='cadastro-pdf-txt".(empty($PDF) ? "" : " hidden")."'>Arquivo PDF: <input class='cadastro-input'name='pdf' type='file' onchange='validaForm(this)' /></label>
					<label class='cadastro-senha-txt'>Senha: <input class='cadastro-input' name='senha' type='password' value='' onkeyup='validaForm(this)' /></label>
					<button class='cadastro-pdf-bt' type='button' onClick='addPDF()'>".(empty($PDF) ? "Carregar PDF" : "Atualizar PDF")."</button>
					<button class='cadastro-pdf-bt' type='button' onClick=\"window.open('../VisualizaTCC/?ID=$ID')\"".(empty($ID) ? " disabled='disabled'" : "").">Visualizar TCC</button>
					<button class='cadastro-bt' name='submit' onclick='' disabled='disabled'>".(empty($ID) ? "Cadastrar" : "Atualizar")."</button>
				</form>
				<script src='../JS/Cadastro.js'></script>
				<div class='resultado'>
				</div>
			</div>
		</div>
	</body>
</html>";
?>