<!DOCTYPE html>
<html>
<head>
    <title>simulação de frete</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
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
        <h1>Consulta de CEP</h1>
        <form id="cep-form" method="POST" action="">
            @csrf
            <div class="form-row">
                <div class="col-md-3 form-group">
                    <label for="cep">CEP:</label>
                    <input type="text" class="form-control" id="cep" name="cep" placeholder="CEP">
                </div>
                <div class="col-md-9 form-group">
                    <label for="numero">Nº:</label>
                    <input type="text" class="form-control" id="numero" name="numero" placeholder="Nº">
                </div>
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
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script>
        $(function() {
            function consultarCep() {
                var cep = $('#cep').val();

                $.get('https://viacep.com.br/ws/' + cep + '/json/', function(response) {
                    $('#numero, #logradouro, #bairro, #cidade, #estado').val('');

                    if (!response.erro) {
                        $('#logradouro').val(response.logradouro);
                        $('#bairro').val(response.bairro);
                        $('#cidade').val(response.localidade);
                        $('#estado').val(response.uf);
                    }
                });
            }

            $('#cep-form').submit(function(event) {
                event.preventDefault();
                consultarCep();
            });

            $('#cep, #numero').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });

            $('#cep, #numero').on('keydown', function(event) {
                if (event.keyCode === 13 || event.keyCode === 9) {
                    event.preventDefault();
                    consultarCep();
                }
            });

            $('#cep, #numero').on('blur', function() {
                consultarCep();
            });
        });
    </script>
</body>
</html>
