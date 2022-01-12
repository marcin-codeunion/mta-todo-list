
        function renderElements(elements)
        {
            let listTarget = document.querySelector('div[data-list-target]');
            listTarget.innerHTML = '';
            elements.forEach((element, index, array) => {
                let status = '';
                let finished = ''
                if(element.status > 0) {
                    status = 'checked';
                    finished = 'todo__item--finished';
                }
                let listItem = `
                <div class="todo__item ${finished}">
                    <input type="text" class="todo__itemName" value="${element.name}" data-request-type="update" data-todo-id="${element.todo_id}" data-element-id="${element.id}" />
                    <input type="checkbox" name="status" ${status} data-todo-id="${element.todo_id}" data-element-id="${element.id}" onclick="updateCheckbox(this)" />
                    <a href="#"  data-todo-id="${element.todo_id}" data-element-id="${element.id}"  onClick="destroyElement(this)">Remove</a>
                </div>
                `;
                listTarget.insertAdjacentHTML('beforeend', listItem);
            });
        }

        function loadElements()
        {
            let formData = new FormData();
            formData.append('action', 'select_todos');
            formData.append('todo_id', todo_id);
            fetch(todo_script_object.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => renderElements(data.elements));
        }

        function updateElement(e)
        {
            let formData = new FormData();
            formData.append('action', 'update_todo_name');
            formData.append('id', e.getAttribute('data-element-id'));
            formData.append('name', e.value);
            fetch(todo_script_object.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => loadElements());
        }

        function updateCheckbox(e)
        {
            let formData = new FormData();
            formData.append('action', 'update_todo_status');
            formData.append('id', e.getAttribute('data-element-id'));
            formData.append('status', Number(e.checked));
            fetch(todo_script_object.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => loadElements());
        }

        function destroyElement(e)
        {
            let formData = new FormData();
            formData.append('action', 'destroy_todo');
            formData.append('id', e.getAttribute('data-element-id'));
            fetch(todo_script_object.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => loadElements());
        }

        function storeElement(e)
        {
            if(!e.value) {
                return false;
            }
            let formData = new FormData();
            formData.append('action', 'store_todo');
            formData.append('todo_id', e.getAttribute('data-todo-id'));
            formData.append('name', e.value);
            fetch(todo_script_object.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => loadElements());
            
        }

        window.addEventListener('keyup', function(event) {
            if (event.keyCode == 13) {
                let element = document.activeElement;
                let requestType = element.getAttribute('data-request-type');
                switch(requestType) {
                    case 'update':
                        updateElement(element);
                        break;
                    case 'store':
                        storeElement(element);
                        break;
                    default:
                        break;
                }
            };
        });

        document.addEventListener("DOMContentLoaded", function(event) {
            loadElements();
        });