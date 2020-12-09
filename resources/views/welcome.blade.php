<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <title>{{ env('APP_NAME') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">

    <style>
        :root {
            --line-height: 1.5rem;
            --border-color: #bbb;
            --border-radius: 0.5rem;
            --vertical-padding: 0.5rem;
            --horizontal-padding: calc(var(--vertical-padding) * 2);
        }

        .tree ul {
            padding-top: var(--line-height);
            position: relative;
        }

        .tree li {
            float: left;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding-top: var(--line-height);
            padding-right: var(--horizontal-padding);
            padding-left: var(--horizontal-padding);
        }

        .tree li::before,
        .tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 1px solid var(--border-color);
            width: 50%;
            height: var(--line-height);
        }

        .tree li::after {
            right: auto;
            left: 50%;
            border-left: 1px solid var(--border-color);
        }

        .tree li:only-child::after,
        .tree li:only-child::before {
            display: none;
        }

        .tree li:only-child {
            padding-top: 0;
        }

        .tree li:first-child::before,
        .tree li:last-child::after {
            border: 0 none;
        }

        .tree li:last-child::before {
            border-right: 1px solid var(--border-color);
            border-top-right-radius: var(--border-radius);
        }

        .tree li:first-child::after {
            border-top-left-radius: var(--border-radius);
        }

        .tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 1px solid var(--border-color);
            height: var(--line-height);
        }

        .tree li a {
            border: 1px solid var(--border-color);
            padding: var(--vertical-padding) var(--horizontal-padding);
            text-decoration: none;
            color: #666;
            display: inline-block;
            border-radius: var(--border-radius);
            position: relative;
        }

        .tree li a.men {
            background-color: cyan;
        }

        .tree li a.women {
            background-color: magenta;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="#">{{ env('APP_NAME') }}</a>
        <button class="btn btn-primary" id="btn-create" type="button">
            Tambah Orang
        </button>
        <div class="modal fade" id="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form>
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Form Orang</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="form-alert" role="alert" style="display: none"></div>
                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama">
                            </div>
                            <div class="form-group">
                                <label for="gender">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <label for="parent_id">Orang Tua</label>
                                <select name="parent_id" id="parent_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btn-delete" data-json="" style="display: none">Hapus</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btn-submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <div class="tree"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
    <script>
        $(() => {
            let person = [];
            let gender = [
                {
                    id: 1,
                    name: 'Laki-laki',
                },
                {
                    id: 2,
                    name: 'Perempuan',
                },
            ];

            const $tree = $('.tree');
            const $btnCreate = $('#btn-create');
            const $btnSubmit = $('#btn-submit');
            const $btnDelete = $('#btn-delete');
            const $formAlert = $('#form-alert')
            const $modal = $('#modal');
            const $form = $modal.find('form');
            const $name = $('#name');
            const $gender = $('#gender');
            const $parent_id = $('#parent_id');

            const fetchPerson = () => {
                $.get('/api/person', response => {
                    person = response.data;

                    renderPersonOptions();
                    renderPersonTree();
                });
            };

            const renderPersonTree = () => {
                let html = '';

                const personHTMLBuilder = person => {
                    person.forEach(row => {
                        const {id, name, gender} = row;
                        const colorClassName = gender === '2' ? 'women' : 'men';

                        html += `<li><a href="#" class="${colorClassName} btn-edit" data-id="${id}">${name}</a>`;

                        if (row.hasOwnProperty('children')) {
                            html += '<ul>';
                            personHTMLBuilder(row.children);
                            html += '</ul>';
                        }

                        html += '</li>';
                    });
                }

                html += '<ul>';
                personHTMLBuilder(person);
                html += '</ul>';

                $tree.html(html);
            };

            const renderGenderOptions = () => {
                let html = `<option value="" hidden>-- Pilih jenis kelamin --</option>`;

                gender.forEach(({id, name}) => html += `<option value="${id}">${name}</option>`);

                $gender.html(html);
            };

            const renderPersonOptions = () => {
                let unnestedPerson = [];

                const denestedPerson = (person) => {
                    person.forEach(row => {
                        unnestedPerson.push(row);

                        if (row.hasOwnProperty('children')) {
                            denestedPerson(row.children);
                        }
                    });
                }

                denestedPerson(person);
                unnestedPerson.sort((a, b) => {
                    if (a.id < b.id) {
                        return -1;
                    }
                    if (a.id > b.id) {
                        return 1;
                    }

                    return 0;
                });

                let html = `<option value="" hidden>-- Pilih orang tua --</option>`;

                unnestedPerson.forEach(({id, name}) => html += `<option value="${id}">${name}</option>`);

                $parent_id.html(html);
            };

            const handleClickBtnCreate = () => {
                $btnDelete.hide();
                $formAlert
                    .removeAttr('class')
                    .hide();
                $form.trigger('reset');
                $form
                    .attr('action', '/api/person')
                    .attr('method', 'POST');
                $modal.modal('show');
            };

            const handleSubmit = event => {
                event.preventDefault();

                let data = {};

                $form
                    .serializeArray()
                    .forEach(item => {
                        if (item.value) {
                            data[item.name] = item.value;
                        }
                    });

                $.ajax({
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    url: $form.attr('action'),
                    method: $form.attr('method'),
                    data: JSON.stringify(data),
                    beforeSend: () => {
                        $formAlert
                            .removeAttr('class')
                            .hide();

                        $btnSubmit
                            .attr('disabled', true)
                            .text('Menyimpan...');
                    },
                    success: response => {
                        $btnSubmit
                            .removeAttr('disabled')
                            .text('Simpan');

                        $modal.modal('hide');

                        fetchPerson();
                    },
                    error: error => {
                        $btnSubmit
                            .removeAttr('disabled')
                            .text('Simpan');

                        const { responseJSON } = error;
                        const { errors } = responseJSON;

                        let errorsHTML = '';

                        for (const error in errors) {
                            errorsHTML += `<li>${errors[error]}</li>`;
                        }

                        $formAlert
                            .attr('class', 'alert alert-danger')
                            .show()
                            .html(`<ul class="mb-0">${errorsHTML}</ul>`)
                    },
                });
            };

            const handleClickBtnDelete = event => {
                const $target = $(event.target);
                const {id, name} = JSON.parse($target.data('json'));

                if (confirm(`Anda yakin untuk menghapus ${name}?`)) {
                    $.ajax({
                        headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                        url: `/api/person/${id}`,
                        method: 'DELETE',
                        success: response => {
                            $modal.modal('hide');
                            fetchPerson();
                        },
                        error: error => {
                            alert(error.responseText);
                        }
                    })
                }
            };

            const handleClickBtnEdit = event => {
                const $target = $(event.target);
                const id = $target.data('id');
                const url =`/api/person/${id}`;

                $.get(url, response => {
                    const { data } = response;
                    const { name, gender, parent_id} = data;

                    $form
                        .attr('action', url)
                        .attr('method', 'PUT');
                    $name.val(name);
                    $gender.val(gender);
                    $parent_id.val(parent_id);
                    $btnDelete
                        .data('json', JSON.stringify(data))
                        .show();
                    $modal.modal('show');
                });
            };

            const init = () => {
                $btnCreate.on('click', handleClickBtnCreate);
                $form.on('submit', handleSubmit);
                $('body').on('click', '.btn-edit', handleClickBtnEdit);
                $btnDelete.on('click', handleClickBtnDelete);

                renderGenderOptions();
                fetchPerson();
            };

            init();
        });
    </script>
</body>

</html>
