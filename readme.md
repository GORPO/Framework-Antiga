[ PSEUDO MVC]
	- Estrutuda baseada na arquitetura Model-View-Controller
	- Models s�o as tabelas do banco de dados mapeadas em forma de classes. Desta forma, 95% das tarefas que dizem respeito ao banco ficam concentradas nos modelos
	- Views s�o as p�ginas propriamente ditas. Rotas que tenham como resultado a exibi��o de alguma informa��o sempre utilizar�o Views
	- Controllers s�o respons�veis por gerenciar rotas e executar tarefas que n�o tenham necessidade de renderiza��o de Views. Todos os arquivos localizados na pasta controllers, mesmo que dentro de subpastas, ser�o automaticamente carregados
	- Helpers est�o em uma quarta camada, onde encontram-se fun��es auxiliares usadas em toda a aplica��o, desde formata��es e valida��es at� manipula��o do banco. Todos os arquivos desta pasta ser�o automaticamente carregados, portanto caso haja necessidade de criar um novo helper, basta jog�-lo nessa pasta


[ USO DO RECAPTCHA ]
	- Configurar as chaves do recaptcha no arquivo lib/pacote.php
	- Incluir a biblioteca atrav�s da fun��o: pacote("recaptcha")
	- Para exibir o campo no formul�rio: echo recaptcha_get_html($_GET["publickey"])
	- Para validar o campo: if (validar_recaptcha($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]))
	- Outra forma de validar o campo �: if (validar_recaptcha()), sem par�metros


[ USO DE TEMPLATES ]
	- Criar os arquivos desejados na pasta templates
	- Para carregar a template, usar a fun��o: template("header", "body")
		* N�o � necess�rio informar o .php no nome dos arquivos
		* Podem ser passados at� 3 par�metros com templates para serem inclu�das


[ IDENTIFICA��O DE ACESSO VIA MOBILE ]
	- Todo acesso � automaticamente validado para verificar a origem do acesso
	- Se desejar que uma p�gina tenha uma vers�o mobile, deve ser criado um arquivo com a mesma estrutura de diret�rios do original, por�m na pasta application_mobile. Ex.: application/home/index.php deve ter um arquivo application_mobile/home/index.php
	- Caso o acesso seja feito de um celular e n�o existir o arquivo referente na pasta application_mobile, a vers�o normal do arquivo � carregada
	- Por conven��o, as templates mobile seguem o padr�o arquivo_mobile.php. Ex.: header_mobile.php, footer_mobile.php


[ MODELS ]
	- Cada tabela do banco de dados deve possuir um arquivo contendo uma classe na pasta de modelo. Por conven��o, nomes de tabelas sempre no plural e nomes de classes sempre no singular. Ex.: tabela clientes, classe Cliente
	- A classe deve herdar da classe Table, que cont�m os m�todos b�sicos para manipula��o de tabelas. Ex.: class Cliente extends Table{ }
	- Deve haver obrigatoriamente na classe uma constante TABLENAME que define o nome da tabela no banco. Ex.: const TABLENAME = "clientes"
	[ RELACIONAMENTOS ]
		$belongs_to: usado quando existe uma chave estrangeira na tabela que faz refer�ncia a outro registro de uma classe qualquer. Ex.: Classe Cliente $belongs_to = array("cidade_natal" => array("class" => "Cidade", "field" => "cidade_id"))
		$has_many: usado de forma reversa ao $belongs_to. Ex.: Classe Cidade $has_many = array("clientes" => array("class" => "Cliente", "field" => "cidade_id"))
		$has_many through: usado para relacionamentos muitos-para-muitos. Caso um Cliente possa ter v�rias cidades e um Cidade possa pertencer a v�rios clientes, ter�amos como exemplo: Classe Cliente $has_many = array("cidades" => array("through" => "ClienteCidade", "field" => "cidade_id", "foreign_key" => "cliente_id", "class" => "Cidade"))
		$has_one: similar ao $has_many mas para relacionamentos um-para-um. Tamb�m funciona de forma reversa ao $belongs_to. Ex.: Classe Cliente $has_one = array("endereco" => array("field" => "cliente_id", "class" => "Endereco"))
	[ COMPORTAMENTO ACTS_AS_LIST ]
		- Usado para tabelas que possuam a necessidade de funcionar como uma lista ordenada de registros
		- � obrigat�rio um campo que controle a posi��o e um par�metro adicional scope define qual o contexto dentro da tabela deve ser considerado uma lista
		- Assim pode haver mais de uma lista dentro de uma mesma tabela
		- Ex.: Classe Cliente $acts_as_list = array("field" => "posicao", "scope" => array("sexo")). Nesse exemplo, a tabela ter� um controle de lista diferente para cada sexo existente (uma lista para homens e uma para mulheres)
		- Usando $acts_as_list, estar�o dispon�veis v�rios m�todos para manipula��o dos registros na classe Table
	[ M�TODOS DISPON�VEIS ATRAV�S DA CLASSE TABLE ]
		ignoredWords(): retorna array
			Retorna uma lista de palavras que podem ser usadas futuramente para melhorar buscas
		find($id [, $include_relationships = true]): retorna mysql_fetch_array
			Encontra o registro na tabela que possui $id passado como par�metro. O par�metro opicional $include_relationships define se os relacionamentos de has_many, belongs_to e has_one devem ser considerados e inclu�dos no resultado em forma de array multidimensional
		findBy($field, $value, [, $include_relationships = true]): retorna mysql_fetch_array
			Encontra o registro na tabela que possui o campo $field igual a $value. $include_relationships funcione de maneira an�loga ao m�todo find()
		findRandom([$include_relationships = true]): retorna mysql_fetch_array
			Retorna um registro aleat�rio da tabela. $include_relationships funcione de maneira an�loga ao m�todo find()
		listOf($id, $relationship [, $order = "nome"]): returna um array de mysql_fetch_array
			Retorna um array contendo os registros referentes aos relacionamentos has_many definidos previamente na classe
		fields(): retorna array
			Retorna a lista de campos da tabela
		all([$order = "default_field"]): returna mysql_query
			Retorna uma query contendo todos os registros da tabela ordenados pelo par�metro $order. Caso n�o seja informado este par�metro, a ordem ser� assumida primeiramente pelos campos do comportamento acts_as_list (quanto for o caso, para ordenar pela posi��o da lista), secundariamente pelo campo "nome" e terciariamente pelo "id"
		collection([$condicoes = array() [, $order = "default_field"]]): retorna mysql_query
			Retorna uma query contendo todos os registros da tabela que atendam as $condicoes passadas como par�metro. As condi��es podem ser passadas como array("campo" => "valor") ou como string "campo > 'valor'". O par�metro $order funciona analogamente ao m�todo all()
		count(): retorna inteiro
			Retorna o n�mero total de registros da tabela
		first(): retorna mysql_fetch_array
			Retorna o primeiro registro da tabela
		last(): retorna mysql_fetch_array
			Retorna o �ltimo registro da tabela
		criar($valores): retorna mysql_fetch_array ou false
			Executa a inser��o de registros na tabela. Os $valores passados devem ser do tipo array("campo" => "valor"). Usando relacionamentos has_many through � poss�vel salvar os registros das tabelas de liga��o automaticamente caso sejam apenas ids e n�o contenha mais atributos, por exemplo $valores = array("clientes" => array(2, 6, 9)). Caso utilize o comportamento acts_as_list, o atributo que armazena a posi��o � automaticamente gerado. O retorno da fun��o � o registro criado ou false caso haja erro
		atualizar($valores): retorna boolean
			Executa a atualiza��o de registro na tabela. Juntamente com os $valores deve ser passado o id do registro a ser alterado. Os demais comportamentos s�o similares ao m�todo criar()
		remover($id): retorna boolean
			Remove o registro da tabela que possua o $id passado como par�metro. Caso use o comportamente acts_as_list, a posi��o dos elementos da lista � automaticamente ajustado
		mapsIds($id, $relationship): retorna array
			Retorna um array contendo somente os ids dos registros encontrados nos relacionamentos. Tem o mesmo retorno da fun��o listOf, por�m somente com os ids e n�o todos os campos
		removerDependentes($id, $relacionamento): sem retorno
			Remove os registros de acordo com o relacionamento $has_many passado. N�o � aconselh�vel o uso deste m�todo, pois � removido somente o registro da tabela e caso o m�todo remover() tenha sido sobrescrito na classe do relacionamento ele n�o ser� levado em considera��o
	[ M�TODOS DISPON�VEIS PARA O COMPORTAMENTO ACTS_AS_LIST ]
		moveUpOnList($id): retorna boolean
			Move para cima na lista o registro com o id igual ao $id passado como par�metro
		moveDownOnList($id): retorna boolean
			Move para baixo na lista o registro com o id igual ao $id passado como par�metro
		lastRecordOnList([$id = 0]): retorna mysql_fetch_array ou boolean
			Retorna o �ltimo registro da lista. O par�metro $id deve ser passado somente quando utilizar scope no acts_as_list
		lastPositionOnList([$id = 0]): retorna inteiro
			Retorna a �ltima posi��o ocupada na lista. O par�metro $id funciona de forma an�loga ao m�todo anterior
		isLastOnList($id): retorna boolean
			Retorna se o registro com o $id passado como par�metro � o �ltimo da lista
		isFirstOnList($id): retorna boolean
			Retorna se o registro com o $id passado como par�metro � o primeiro da lista
		whereClauseFromScope($id): retorna string
			Retorna uma string contendo as condi��es a serem usadas nas consultas SQL para se obter corretamente os registros da lista. Usado somente se o acts_as_list tiver scope


[ IMAGENS, CSS e JAVASCRIPT ]
	- Esses arquivos s�o armazenados na pasta assets, em suas respectivas pastas
	- Os �cones utilizados devem estar na pasta assets/images/18x18, para que possam ser carregados pela fun��o img()
		* A fun��o img() carrega uma imagem da pasta 18x18. Ex.: echo img("edit", 'title="Editar"')
		* N�o � obrigat�rio especificar a extens�o da imagem. Por padr�o ser� assumido que seja .png
		* Pode ser usada para carregar outras imagem informando o caminho relativo. Ex.: para carregar a imagem assets/images/template/logo.php, use: echo img("../template/logo")
	- Para utilizar favicon, basta existir um arquivo favicon.png ou favicon.ico na pasta assets/images e ele ser� carregado automaticamente


[ ENVIO DE EMAIL ]
	- O envio de email � feito atrav�s da fun��o enviar_email($destinatario, $assunto, $corpo)
	- Pode ser feito atrav�s de SMTP ou pela fun��o mail() do PHP. Para definir a forma de envio, edite o helper de email alterando a chamada da fun��o enviar_email() para a fun��o convencional. Por padr�o o envio � feito por SMTP
	- A configura��o dos dados do servidor SMTP deve ser feita tamb�m neste arquivo
	- As fun��es de envio s�o independentes, existindo enviar_email_smtp() e enviar_email_convencional(), e a enviar_email() faz uma chamada a uma dessas duas fun��es (SMTP por padr�o)


[ FORMUL�RIOS ]
	- A gera��o de formul�rios pode ser facilitada atrav�s de fun��es.
	- A tag <form> pode ser substituida pela fun��o form([$options = array()]). Dois par�metros importantes podem ser passados atrav�s das $options:
		- values: um array de elementos do tipo "campo" => "valor" que ser� usado para preencher automaticamente os campos do formul�rios com os respectivos nomes. Ex.: echo form(array("values" => array("nome" => "Pedro", "nascimento" => "10/10/2000"))). Normalmente o array de values ser� o pr�prio $_POST
		- alinhamento: dita o alinhamento dos labels dentro de cada elemento do form
	- O fechamento da tag (</form>) pode ser substituido pela fun��o end_form(). Ex.: echo end_form()
	- Os campos do formul�rio devem estar contidos em uma tag <table> para ser gerada a grade corretamente. As fun��es auxiliares para gerar campos s�o:
		- commom_field([$options = array()]): � a fun��o gen�rica para gera��o de campos. Todas as demais fun��es utilizam ela para gerar o html necess�rio. S� deve ser usado quando for preciso criar um campo ou uma linha na grade do formul�rio diferente do padr�o
		- text_field([$options = array()]): gera um campo de texto padr�o
		- password_field([$options = array()]): gera um campo de senha padr�o
		- telefone_field([$options = array()])
		- cnpj_field([$options = array()])
		- cpf_field([$options = array()])
		- cep_field([$options = array()])
		- textarea([$options = array()]): rows e cols podem ser passadas como "size" => "<cols>x<rows>"
		- button([$options = array()])
		- submit([$options = array()]): $options neste caso pode ser uma string, que ser� o value do button
		- select([$params = array()])
		- options_for_select($options[, $selecionado = ""[, $prompt = false]]): gera uma string de <options> de acordo com o array de $options passado por par�metro. $prompt � o texto inicial a ser mostrado, por exemplo "-- SELECIONE --"
		- options_for_select_from_collection($collection, $value, $label[, $selecionado = ""[, $prompt = true]]): gera uma string de <options> de acordo com a query ou array passado na $collection. $value ser� campo (chave do array $collection) que ser� o atributo value da option e $label ser� o campo (chave do array $collection) que ser� o texto exibido nela
	- O uso mais detalhado de cada fun��o pode ser visto no helper de formul�rios