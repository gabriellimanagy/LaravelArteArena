<!DOCTYPE html>
<html>
<head>
    <title>Simulação de Frete</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        .form-group {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('bandeiras') }}" class="btn btn-outline-success">Calculadora de Bandeira</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}" class="btn btn-outline-success">Cálculo de Frete</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1>Calcular Frete</h1>
        <div class="row">
            <div class="col-md-6">
                <form id="produto-form" method="POST" action="">
                    @csrf
                    <div class="form-group">
                        <label for="produto">Produto:</label>
                        <select class="form-control" id="produto" name="produto">
                            <option value="">Selecione um produto</option>
                            <?php
                                // Conexão com o banco de dados
                                $servername = "localhost";
                                $username = "root";
                                $password = "1234";
                                $dbname = "db_artearena";

                                // Criar conexão
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                // Verificar conexão
                                if ($conn->connect_error) {
                                    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
                                }

                                // Consulta para recuperar os produtos do banco de dados
                                $sql = "SELECT ID, nome FROM tabela_produtos";
                                $result = $conn->query($sql);

                                // Exibir os produtos no select2
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row["ID"] . '">' . $row["nome"] . '</option>';
                                    }
                                }

                                // Fechar conexão
                                $conn->close();
                            ?>
                        </select>
                    </div>
                </form>
                <div class="form-group mt-4">
                    <label for="valor">Valor:</label>
                    <input type="text" class="form-control" id="valor" name="valor" readonly>
                </div>
                <div class="form-group">
                    <label for="peso">Peso:</label>
                    <input type="text" class="form-control" id="peso" name="peso" readonly>
                </div>
                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" value="1">
                </div>
            </div>
            <div class="col-md-6">
                <form id="cep-form" method="POST" action="">
                    @csrf
                    <div class="form-group">
                        <label for="cep">CEP:</label>
                        <input type="text" class="form-control" id="cep" name="cep" placeholder="CEP">
                    </div>
                    <div class="form-group">
                        <label for="logradouro">Logradouro:</label>
                        <input type="text" class="form-control" id="logradouro" name="logradouro" readonly>
                    </div>
                    <div class="form-group">
                        <label for="bairro">Bairro:</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cidade">Cidade:</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" readonly>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <input type="text" class="form-control" id="estado" name="estado" readonly>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Transportadoras:</h3>
                <div id="cardsContainer"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(function() {
            // Inicializar o select2 para o campo de produto
            $('#produto').select2();

            function consultarProduto() {
                var produto = $('#produto').val();

                // Lógica para consultar dados do produto
                if (produto === 'produto1') {
                    $('#valor').val('10.00');
                    $('#peso').val('1.5');
                } else if (produto === 'produto2') {
                    $('#valor').val('15.00');
                    $('#peso').val('2.0');
                } else if (produto === 'produto3') {
                    $('#valor').val('20.00');
                    $('#peso').val('0.5');
                } else {
                    // Realizar requisição AJAX para obter dados do produto do Tiny API
                    $.get('https://api.tiny.com.br/api2/produto.obter.php', {
                        token: 'bc3cdea243d8687963fa642580057531456d34fa',
                        id: produto,
                        formato: 'json'
                    }, function(response) {
                        var produtoData = JSON.parse(response);
                        $('#valor').val(produtoData.retorno.produto.preco);
                        $('#peso').val(produtoData.retorno.produto.peso_bruto);
                    });
                }
            }

            $('#produto').change(function() {
                consultarProduto();
            });

            function consultarCep() {
                var cep = $('#cep').val();

                $.get('https://viacep.com.br/ws/' + cep + '/json/', function(response) {
                    $('#logradouro').val('');
                    $('#bairro').val('');
                    $('#cidade').val('');
                    $('#estado').val('');

                    if (!response.erro) {
                        $('#logradouro').val(response.logradouro);
                        $('#bairro').val(response.bairro);
                        $('#cidade').val(response.localidade);
                        $('#estado').val(response.uf);
                    }
                });
            }

            $('#cep').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });

            $('#cep').on('keydown', function(event) {
                if (event.keyCode === 13 || event.keyCode === 9) {
                    event.preventDefault();
                    consultarCep();
                }
            });

            $('#cep').on('blur', function() {
                consultarCep();
            });

            $('#cep').on('blur', function(event) {
                event.preventDefault();

                const url = "http://f877-2804-1b3-a243-1d72-a82d-dfdd-5cdb-55d2.ngrok-free.app/consultar-kangu";
                const cepOrigem = "04781000";
                const cepDestino = $('#cep').val();
                const produto = [
                    {
                        peso: 10,
                        valor: 600,
                        quantidade: 1
                    }
                ];
                const bodyData = {
                    cepOrigem: cepOrigem,
                    cepDestino: cepDestino,
                    produtos: produto
                };

                fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(bodyData)
                })
                    .then(response => response.json())
                    .then(data => {
                        const transportadoras = data.transporte;

                        const cardsContainer = document.getElementById("cardsContainer");

                        while (cardsContainer.firstChild) {
                            cardsContainer.removeChild(cardsContainer.firstChild);
                        }

                        // Iterar sobre as transportadoras e criar os cards
                        transportadoras.forEach(transportadora => {
                            // Criar o elemento do card
                            const cardElement = document.createElement("div");
                            cardElement.classList.add("card");
                            cardElement.style.margin = "auto";

                            // Criar os elementos para exibir os dados relevantes da transportadora
                            const nomeElement = document.createElement("h3");
                            nomeElement.textContent = transportadora.nome;

                            const logoElement = document.createElement("img");
                            logoElement.src = transportadora.logo;
                            logoElement.style.width = "140px"; // Defina a largura desejada
                            logoElement.style.height = "auto"; // A altura será ajustada proporcionalmente

                            const valorFreteElement = document.createElement("p");
                            valorFreteElement.textContent = `Valor do Frete: ${transportadora.valorFrete}`;

                            const prazoEntregaElement = document.createElement("p");
                            prazoEntregaElement.textContent = `Prazo de Entrega: ${transportadora.prazoEntrega} dias`;

                            const dataPrevEntregaElement = document.createElement("p");
                            dataPrevEntregaElement.textContent = `Previsão de Entrega: ${transportadora.dataPrevEntrega}`;

                            // Adicionar os elementos do card à div do container
                            cardElement.appendChild(nomeElement);
                            cardElement.appendChild(logoElement);
                            cardElement.appendChild(valorFreteElement);
                            cardElement.appendChild(prazoEntregaElement);
                            cardElement.appendChild(dataPrevEntregaElement);

                            // Adicionar o card ao container na página
                            cardsContainer.appendChild(cardElement);
                        });
                    })
                    .catch(error => console.error(error));
            });
        });
    </script>
</body>
</html>
