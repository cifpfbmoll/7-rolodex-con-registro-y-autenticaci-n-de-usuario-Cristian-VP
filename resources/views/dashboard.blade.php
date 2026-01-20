<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rolodex Digital') }}
            </h2>
            <a href="{{ route('contacts.export') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                📥 Descargar CSV
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulario de entrada (Columna izquierda) -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Agregar Contacto</h3>

                            <form id="contactForm" class="space-y-4">
                                @csrf

                                <!-- Nombre Completo -->
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre Completo
                                    </label>
                                    <input
                                        type="text"
                                        id="full_name"
                                        name="full_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Victor Frankenstein"
                                        required
                                    >
                                    <span class="error-message text-red-500 text-sm hidden"></span>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Número de Teléfono
                                    </label>
                                    <input
                                        type="tel"
                                        id="phone_number"
                                        name="phone_number"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="555-776-2323"
                                        required
                                    >
                                    <span class="error-message text-red-500 text-sm hidden"></span>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email_address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Dirección de Email
                                    </label>
                                    <input
                                        type="email"
                                        id="email_address"
                                        name="email_address"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="doctor@nodedojo.com"
                                        required
                                    >
                                    <span class="error-message text-red-500 text-sm hidden"></span>
                                </div>

                                <!-- Botón Guardar -->
                                <button
                                    type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                                >
                                    💾 Guardar Contacto
                                </button>

                                <!-- Mensaje de error/éxito -->
                                <div id="formMessage" class="hidden p-3 rounded-lg text-sm"></div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabla de contactos (Columna derecha) -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Mis Contactos</h3>

                            <div class="overflow-x-auto">
                                <table class="w-full" id="contactsTable">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Teléfono</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contactsBody" class="divide-y divide-gray-200">
                                        <!-- Los contactos se cargarán aquí dinámicamente -->
                                    </tbody>
                                </table>
                                <div id="emptyMessage" class="text-center py-8 text-gray-500">
                                    No hay contactos aún. ¡Agrega tu primer contacto!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos iniciales de contactos
        let contacts = @json($contacts);

        // Función para renderizar la tabla
        function renderContactsTable() {
            const tbody = document.getElementById('contactsBody');
            const emptyMessage = document.getElementById('emptyMessage');

            if (contacts.length === 0) {
                tbody.innerHTML = '';
                emptyMessage.classList.remove('hidden');
                return;
            }

            emptyMessage.classList.add('hidden');
            tbody.innerHTML = contacts.map(contact => `
                <tr class="hover:bg-gray-50 transition" data-contact-id="${contact.id}">
                    <td class="px-4 py-3 text-sm text-gray-900">${contact.full_name}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${contact.phone_number}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${contact.email_address}</td>
                    <td class="px-4 py-3 text-center">
                        <button class="delete-btn inline-block px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm" data-contact-id="${contact.id}">
                            🗑️ Eliminar
                        </button>
                    </td>
                </tr>
            `).join('');

            // Agregar event listeners a los botones de eliminar
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', deleteContact);
            });
        }

        // Función para agregar un nuevo contacto
        document.getElementById('contactForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(document.getElementById('contactForm'));
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('{{ route("contacts.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (response.ok) {
                    // Agregar el nuevo contacto a la lista
                    contacts.push(result.contact);
                    renderContactsTable();

                    // Limpiar formulario
                    document.getElementById('contactForm').reset();

                    // Mostrar mensaje de éxito
                    showMessage(result.message, 'success');
                } else {
                    // Mostrar errores de validación
                    showValidationErrors(result.errors || {});
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error al guardar el contacto', 'error');
            }
        });

        // Función para eliminar un contacto
        async function deleteContact(e) {
            const contactId = e.target.dataset.contactId;
            const contact = contacts.find(c => c.id == contactId);

            if (!confirm(`¿Estás seguro de que deseas eliminar a ${contact.full_name}?`)) {
                return;
            }

            try {
                const response = await fetch(`/contacts/${contactId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    // Eliminar de la lista local
                    contacts = contacts.filter(c => c.id != contactId);
                    renderContactsTable();
                    showMessage(result.message, 'success');
                } else {
                    showMessage(result.error || 'Error al eliminar el contacto', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error al eliminar el contacto', 'error');
            }
        }

        // Función para mostrar mensajes
        function showMessage(message, type) {
            const messageDiv = document.getElementById('formMessage');
            messageDiv.textContent = message;
            messageDiv.className = `p-3 rounded-lg text-sm ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            messageDiv.classList.remove('hidden');

            setTimeout(() => {
                messageDiv.classList.add('hidden');
            }, 3000);
        }

        // Función para mostrar errores de validación
        function showValidationErrors(errors) {
            // Limpiar errores previos
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });

            // Mostrar nuevos errores
            Object.keys(errors).forEach(field => {
                const errorSpan = document.querySelector(`[name="${field}"] ~ .error-message`);
                if (errorSpan) {
                    errorSpan.textContent = errors[field][0];
                    errorSpan.classList.remove('hidden');
                }
            });
        }

        // Renderizar contactos iniciales al cargar la página
        document.addEventListener('DOMContentLoaded', renderContactsTable);
    </script>
</x-app-layout>
