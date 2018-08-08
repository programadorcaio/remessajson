<?

function removeAcentos($str) {
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
    // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
    $str = preg_replace('/[^a-z0-9]/i', ' ', $str);
    $str = preg_replace('/_+/', ' ', $str); // ideia do Bacco :)
    return $str;
}


header('Content-Type: text/html; charset=utf-8');
require_once('../../sistema/Connections/localhost.php'); 

$id 								= "1";
$id_auto_incremento					= "1";

$empresa_beneficiaria 				= "5034445";
$numero_controle_participante		= "";
$percentual_multa					= "0200";
$numero_documento					= $id;

$dia = date('d');
$mes = date('m');
$ano = substr(date('y'),0,2);
$data_emissao_titulo				= $dia . $mes. $ano;

$data_limite_do_desconto			= "";
$valor_do_desconto					= "";
$valor_do_iof						= "";
$valor_do_abatimento				= "";
$codigo_da_empresa					= "5034445";

//Máximo 30 c
$razao_social						= "SEO SISTEMAS INFORMATIZADOS";

//DDMMAA
$data_hoje_dia						= date('d');
$data_hoje_mes						= date('m');
$data_hoje_ano						= date('Y');
$data_hoje							= $data_hoje_dia . $data_hoje_mes . substr($data_hoje_ano,2,2);

//Identificação do Registro
//0
//001 a 001 (1)
$remessa = str_pad('0', 1,' ');

//Identificação do Arquivo Remessa
//1
//002 a 002 (1)
$remessa .= str_pad('1', 1,' ');

//Literal Remessa
//REMESSA
//003 a 009 (7)
$remessa .= str_pad('REMESSA', 7,' ');

//Código de Serviço
//01
//010 a 011 (2)
$remessa .= str_pad('01', 2,' ');

//Literal Serviço
//COBRANCA
//012 a 026 (15)
$remessa .= str_pad('COBRANCA', 15,' ');


//Código da Empresa
//Será fornecido pelo Bradesco, quando do  Cadastramento Vide Obs. Pág. 16
//027 a 046 (20)
$remessa .= str_pad($codigo_da_empresa, 20,'0',STR_PAD_LEFT);

//Nome da Empresa
//Razão Social
//047 a 076 (30)
$remessa .= str_pad($razao_social, 30,' ');

//Número do Bradesco na Câmara de Compensação
//237
//077 a 079 (3)
$remessa .= str_pad('237', 3,' ');

//Nome do Banco por Extenso
//Bradesco
//080 a 094 (15)
$remessa .= str_pad('BRADESCO', 15,' ');

//Data da Gravação do Arquivo
//DDMMAA Vide Obs. Pág. 16
//095 a 100 (6)
$remessa .= str_pad($data_hoje, 6,' ');

//Branco
//Branco
//101 a 108 (8)
$remessa .= str_pad('', 8,' ');

//Identificação do sistema
//MX Vide Obs. Pág. 16
//109 a 110 (2)
$remessa .= str_pad('MX', 2,' ');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Nº Seqüencial de Remessa
//Seqüencial Vide Obs. Pág. 16
//111 a 117 (7)
$remessa .= str_pad($id, 7,'0',STR_PAD_LEFT);
//SEMPRE UM NUMERO DIFERENTE, REGISTRO ID UNICO



//Branco
//Branco
//118 a 394 (277)
$remessa .= str_pad('', 277,' ');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Nº Seqüencial do Registro de Um em Um
//000001
//395 a 400 (6)
$remessa .= str_pad($id_auto_incremento, 6,'0',STR_PAD_LEFT);

$remessa .= "\r\n";


$id_auto_incremento++;


$hoje = date('Y-m-d');
$mes  = date('m');
$ano  = date('Y');
$query_rsPegaTitulos = "";
$rsPegaTitulos = mysql_query($query_rsPegaTitulos, $localhost) or die(mysql_error());
$totalRows_rsPegaTitulos = mysql_num_rows($rsPegaTitulos);
	
while($pega_titulos = mysql_fetch_assoc($rsPegaTitulos)) {
	
$id_titulos .= $pega_titulos[id].';';
	
$cpf_do_responsavel_pagador			= str_replace('.','',str_replace('-','',$pega_titulos['cnpj']));
$nome_do_responsavel_pagador		= strtoupper(removeAcentos($pega_titulos['razao_social']));
$nome_do_responsavel_pagador		= str_replace("CENTRO EDUCACIONAL", "C.E.", $nome_do_responsavel_pagador);
$endereco_do_responsavel_pagador	= removeAcentos($pega_titulos['end_responsavel1']) . ' ' 
									. $pega_titulos['endnumero_responsavel1'] .' ' 
									. removeAcentos($pega_titulos['bairro_responsavel1']);

$cep_do_responsavel_pagador			= substr($pega_titulos['cep'],0,5);
$cep_sufixo_do_responsavel_pagador	= substr($pega_titulos['cep'],6,3);
$primeira_mensagem					= "";
$segunda_mensagem					= "APOS O VENCIMENTO MULTA DE 2% E MORA DIA DE 0,33%";
//DDMMAA
$dd 								= substr($pega_titulos['vencimento'],8,2);
$mm 								= substr($pega_titulos['vencimento'],5,2);
$aa 								= substr($pega_titulos['vencimento'],2,2);
$vencimento_titulo					= $dd.$mm.$aa;
$valor_titulo						= $pega_titulos['valor'];
$valor_titulo_sem_ponto				= str_replace('.','',str_replace(',','',$valor_titulo));
$I = '0.33'/100.00;
//CALCULAR PORCENTAGEM/PERCENTAGEM JUROS
$valor_mora_dia_atraso = str_replace(',','',substr(number_format(str_replace(',', '.', ($valor_titulo*9.9 / 100 / 30)), 3, ',', ''), 0, -1));


if($nome_do_responsavel_pagador == ""){
	echo "Impossível gerar o arquivo, nome do responsável pelo aluno $pega_titulos[nome] não foi preenchido.";	
	die();
}
else if($endereco_do_responsavel_pagador == ""){
	echo "Impossível gerar o arquivo, endereço do responsável pelo aluno $pega_titulos[nome] não foi preenchido.";	
	die();
}
else if($cep_do_responsavel_pagador == ""){
	echo "Impossível gerar o arquivo, cep do responsável pelo aluno $pega_titulos[nome] não foi preenchido.";	
	die();
}
else if($cpf_do_responsavel_pagador == ""){
	echo "Impossível gerar o arquivo, CNPJ do responsável pelo aluno $pega_titulos[razao_social] não foi preenchido. ";	
	die();
}

$identificacao_numero_bancario		= str_pad($pega_titulos[id], 11, "0", STR_PAD_LEFT);

//numeros da carteira
$confere_cart_1 = 0 * 2;
$confere_cart_2 = 9 * 7;
$confere_1 = substr($identificacao_numero_bancario,0,1) * 6;
$confere_2 = substr($identificacao_numero_bancario,1,1) * 5; 
$confere_3 = substr($identificacao_numero_bancario,2,1) * 4; 
$confere_4 = substr($identificacao_numero_bancario,3,1) * 3; 
$confere_5 = substr($identificacao_numero_bancario,4,1) * 2; 
$confere_6 = substr($identificacao_numero_bancario,5,1) * 7; 
$confere_7 = substr($identificacao_numero_bancario,6,1) * 6; 
$confere_8 = substr($identificacao_numero_bancario,7,1) * 5; 
$confere_9 = substr($identificacao_numero_bancario,8,1) * 4; 
$confere_10 = substr($identificacao_numero_bancario,9,1) * 3; 
$confere_11 = substr($identificacao_numero_bancario,10,1) * 2; 

$resultado_conferes = ($confere_cart_1 + $confere_cart_2 + $confere_1 + $confere_2 + $confere_3 + $confere_4 + $confere_5 + $confere_6 + $confere_7 + $confere_8 + $confere_9 + $confere_10 + $confere_11);
$resultado_conferes_resto = $resultado_conferes % 11;

$nosso_numero_confere = (11 - $resultado_conferes_resto);

if($resultado_conferes_resto == 1) { 
	$identificacao_numero_bancario_conferencia = "P"; 
}
else if($resultado_conferes_resto == 0) {
	$identificacao_numero_bancario_conferencia = 0;
}
else { 	
	$identificacao_numero_bancario_conferencia = $nosso_numero_confere;
	
} 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////
//AQUI COMEÇA O ARQUIVO DE REMESSA  ///////////////
///////////////////////////////////////////////////

//Identificação do Registro
$remessa .= '1';
//Agência de Débito(opcional)
//Código da Agência do Pagador Exclusivo para Débito em Conta Vide Obs. Pág. 16
$remessa .= str_pad('', 5,'0');
//Dígito da Agência de Débito (opcional)
$remessa .= str_pad('', 1,'0');
//Razão da Conta Corrente(opcional)
$remessa .= str_pad('', 5,'0');
//Conta Corrente(opcional)
//Número da Conta do Pagadora Vide Obs. Pág. 16
$remessa .= str_pad('', 7,'0');
//Dígito da Conta Corrente (opcional)
$remessa .= str_pad('', 1,'0');
//Identificação da Empresa Beneficiária no Banco
//Zero, Carteira, Agência e Conta Corrente Vide Obs. Pág. 16
//021 a 037 (17)
$remessa .= str_pad($empresa_beneficiaria, 17, "0", STR_PAD_LEFT);
//Nº Controle do Participante
//Uso da Empresa Vide Obs. Pág. 17
//038 a 062 (25)
$remessa .= str_pad($numero_controle_participante, 25,' ');
//Código do Banco a ser debitado na Câmara de Compensação
//Nº do Banco “237” Vide Obs. Pág. 17
//063 a 065 (3)
$remessa .= str_pad('000', 3,' ');
//Campo de Multa
//Se = 2 considerar percentual de multa. Se = 0, sem multa. Vide Obs. Pág. 17
//066 a 066 (1)
$remessa .= str_pad('2', 1,' ');
//Percentual de multa
//Percentual de multa a ser considerado videObs. Pág. 17
//067 a 070 (4)
$remessa .= str_pad($percentual_multa, 4,'0');
//Identiicação do Título no Banco
//Número Bancário para Cobrança Com e Sem Registro Vide Obs. Pág. 17
//071 a 081 (11)
$remessa .= str_pad($identificacao_numero_bancario, 11,' ');
//Digito de Auto Conferencia do Número Bancário.
//Digito N/N Vide Obs.Pág. 17
//082 a 082 (1)
$remessa .= str_pad($identificacao_numero_bancario_conferencia, 1,' ');
//Desconto Bonificação por dia
//Valor do desconto bonif./dia.
//083 a 092 (10)
$remessa .= str_pad('0', 10,'0');
//Condição para Emissão da Papeleta de Cobrança
//1 = Banco emite e Processa o registro. 2 = Cliente emite e o Banco somente processa o registro – Vide obs. Pág. 19
//093 a 093 (1)
$remessa .= str_pad('2', 1,' ');
//Ident. se emite Boleto para Débito Automático
//N= Não registra na cobrança. Diferente de N registra e emite Boleto. Vide Obs. Pág. 19
//094 a 094 (1)
$remessa .= str_pad('N', 1,' ');
//Identificação da Operação do Banco
//Brancos
//095 a 104 (10)
$remessa .= str_pad('', 10,' ');
//Indicador Rateio Crédito (opcional)
//“R” Vide Obs. Pág. 19
//105 a 105 (1)
$remessa .= str_pad('', 1,' ');
//Endereçamento para Aviso do Débito Automático em Conta Corrente  (opcional)
//Vide Obs. Pág. 19
//106 a 106 (1)
$remessa .= str_pad('2', 1,' ');
//Branco
//Branco
//107 a 108 (2)
$remessa .= str_pad('', 2,' ');
//Identificação da ocorrência
//Códigos de ocorrência Vide Obs. Pág. 20
//109 a 110 (2)
$remessa .= str_pad('01', 2,' ');
//Nº do Documento
//Documento
//111 a 120 (10)
$remessa .= str_pad($numero_documento, 10,' ');
//Data do Vencimento do Título
//DDMMAA Vide Obs. Pág. 20
//121 a 126 (6)
$remessa .= str_pad($vencimento_titulo, 6,' ');
//Valor do Título
//Valor do Título (preencher sem ponto e sem vírgula)
//127 a 139 (13)
$remessa .= str_pad($valor_titulo_sem_ponto, 13,'0',STR_PAD_LEFT);
//Banco Encarregado da Cobrança
//Preencher com zeros
//140 a 142 (3)
$remessa .= str_pad('000', 3,' ');
//Agência Depositária
//Preencher com zeros
//143 a 147 (5)
$remessa .= str_pad('00000', 5,' ');
//Espécie de Título
//01-Duplicata
//02-Nota Promissória 
//03-Nota de Seguro 
//04-Cobrança Seriada 
//05-Recibo
//10-Letras de Câmbio 
//11-Nota de Débito 
//12-Duplicata de Serv. 
//99-Outros
//148 a 149 (2)
$remessa .= str_pad('99', 2,' ');
//Identificação
//Sempre = N
//150 a 150 (1)
$remessa .= str_pad('N', 1,' ');
//Data da emissão do Título
//DDMMAA
//151 a 156 (6)
$remessa .= str_pad($data_emissao_titulo, 6,' ');
//1ª instrução
//Vide Obs. Pág. 20
//157 a 158 (2)
$remessa .= str_pad('00', 2,' ');
//2ª instrução
//Vide Obs. Pág. 20
//159 a 160 (2)
$remessa .= str_pad('00', 2,' ');
//Valor a ser cobrado por dia de Atraso
//Mora por Dia de Atraso Vide obs. Pág. 21
//161 a 173 (13)
$remessa .= str_pad($valor_mora_dia_atraso, 13,'0',STR_PAD_LEFT);
//Data Limite P/Concessão de Desconto
//DDMMAA
//174 a 179 (6)
$remessa .= str_pad($data_limite_do_desconto, 6,'0');
//Valor do Desconto
//Valor Desconto
//180 a 192 (13)
$remessa .= str_pad($valor_do_desconto, 13,'0');
//Valor do IOF
//Valor do IOF – Vide Obs. Pág. 21
//193 a 205 (13)
$remessa .= str_pad($valor_do_iof, 13,'0');
//Valor do Abatimento a ser concedido ou cancelado
//Valor Abatimento
//206 a 218 (13)
$remessa .= str_pad($valor_do_abatimento, 13,'0');
//Identificação do Tipo de Inscrição do Pagador
//01-CPF 
//02-CNPJ 
//03-PIS/PASEP
//98-Não tem
//99-Outros
//219 a 220 (2)
$remessa .= str_pad('01',2,' ');
//Nº Inscrição do Pagador
//CNPJ/ CPF - Vide Obs. Pág. 21
//221 a 234 (14)
$remessa .= str_pad($cpf_do_responsavel_pagador, 14,'0',STR_PAD_LEFT);
//Nome do Pagador
//Nome do Pagador
//235 a 274 (40)
$remessa .= str_pad($nome_do_responsavel_pagador, 40,' ');
//Endereço Completo
//Endereço do Pagador
//275 a 314 (40)
$remessa .= str_pad($endereco_do_responsavel_pagador, 40,' ');
//1ª Mensagem
//Vide Obs. Pág. 22
//315 a 326 (12)
$remessa .= str_pad($primeira_mensagem, 12,' ');
//CEP
//CEP Pagador
//327 a 331 (5)
$remessa .= str_pad($cep_do_responsavel_pagador, 5,' ');
//Sufixo do CEP
//Sufixo
//332 a 334 (3)
$remessa .= str_pad($cep_sufixo_do_responsavel_pagador, 3,' ');
//Sacador/Avalista ou 2ª Mensagem
//Decomposição Vide Obs. Pág. 22
//335 a 394 (60)
$remessa .= str_pad($segunda_mensagem, 59,' ');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Nº Seqüencial do Registro
//Nº Seqüencial do Registro
//395 a 400 (6)
$remessa .= str_pad($id_auto_incremento, 6,'0',STR_PAD_LEFT);
$remessa .= "\r\n";
$id_auto_incremento++;

}



//Lay-out do Arquivo-Remessa - Registro Trailler

//Identificação Registro
//9
//001 a 001 (1)
$remessa .= str_pad('9', 1,' ');

//Branco
//Branco
//002 a 394 (393)
$remessa .= str_pad('', 393,' ');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Número Seqüencial de Registro
//Nº Seqüencial do Último Registro
//395 a 400 (6)
$remessa .= str_pad($id_auto_incremento, 6,'0',STR_PAD_LEFT);



if($id_auto_incremento == 2){
	echo "Não existem boletos.";
	die();
}

$dia = date('d');
$mes = date('m');



$data_hoje_completa = date('Y-m-d');
$hora = date('H:i:s');

$query_rsSelecionaRemessa = "select * from boletos_remessas where escola = '$_SESSION[escola]' and data = '$data_hoje_completa' order by id desc";
$rsSelecionaRemessa = mysql_query($query_rsSelecionaRemessa, $localhost) or die(mysql_error());
$pega_remessas = mysql_fetch_assoc($rsSelecionaRemessa);

$id_sequencial_arquivo = str_pad($pega_remessas[id_sequencial_arquivo] + 1, 2,'0',STR_PAD_LEFT);
$nome_remessa = "cb".$dia."".$mes.$id_sequencial_arquivo.".tst";


// $query_rsInsereRemessa = "INSERT INTO boletos_remessas (nome, remessa, data, hora, id_sequencial_arquivo, banco, escola) VALUES ('$nome_remessa','$remessa','$data_hoje_completa','$hora','$id_sequencial_arquivo','Bradesco','$_SESSION[escola]')";
// $rsInsereRemessa = mysql_query($query_rsInsereRemessa, $localhost) or die(mysql_error());

// $id_remessa = mysql_insert_id();

// $explode_titulos = explode(';',$id_titulos);
// $total_ids = count($explode_titulos);

// for($i=0;$total_ids>$i;$i++){
// 	if($explode_titulos[$i] != '') {
// 		$query_rsAtualizaMensalidade = "UPDATE mensalidades SET data_envio_remessa = '$data_hoje_completa', id_remessa = '$id_remessa' WHERE escola = '$_SESSION[escola]' and id = '$explode_titulos[$i]'";
// 		$rsAtualizaMensalidade = mysql_query($query_rsAtualizaMensalidade, $localhost) or die(mysql_error());
// 	}
// }

$fp = fopen('remessas/'.$nome_remessa,"wb");
//.rem
if(fwrite($fp,$remessa)){ 

header('content-type: text/plain');
header("Content-Disposition: attachment; filename=$nome_remessa");
header('Pragma: no-cache');
}
fclose($fp);

readfile('remessas/'.$nome_remessa); 


?>