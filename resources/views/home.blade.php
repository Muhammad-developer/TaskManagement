<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body
    class="bg-gradient-to-r from-indigo-300 via-purple-300 to-pink-300 text-gray-900 min-h-screen flex flex-col justify-center items-center">
<div class="container mx-auto py-8">
    <h1 class="text-5xl font-extrabold mb-8 text-center text-white drop-shadow-lg animate-bounce">Task Management
    </h1>

    <!-- Button для открытия модального окно -->
    <div class="text-center mb-8">
        <button id="openModal"
                class="bg-gradient-to-r from-green-400 to-blue-500 hover:from-green-500 hover:to-blue-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transform hover:scale-105 transition duration-300 ease-in-out animate-pulse">
            Добавить задачу
        </button>
    </div>

    <!-- Modal -->
    <div id="modal"
         class="hidden fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75 z-50">
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
            <h2 class="text-3xl font-extrabold mb-6 text-center text-gray-800">Новая задача</h2>
            <form id="taskForm" class="mb-4">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-bold mb-2 text-gray-700">Название</label>
                    <input type="text" id="title" name="title"
                           class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-bold mb-2 text-gray-700">Описание</label>
                    <textarea id="description" name="description"
                              class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"></textarea>
                </div>
                <div class="mb-4">
                    <label for="deadline" class="block text-sm font-bold mb-2 text-gray-700">Срок</label>
                    <input type="date" id="deadline" name="deadline"
                           class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out">
                </div>
                <div class="flex justify-end">
                    <button type="button" id="closeModal"
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-full shadow-lg mr-2 transform hover:scale-105 transition duration-300 ease-in-out">
                        Закрыть
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full shadow-lg transform hover:scale-105 transition duration-300 ease-in-out">
                        Добавить задачу
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task List -->
    <div id="taskList" class="mt-8">
        <h2 class="text-3xl font-extrabold mb-4 text-center text-white drop-shadow-lg">Задачи</h2>
        <ul id="tasks" class="bg-white p-4 rounded-lg shadow-2xl">
            <!-- Задачи будут вставляться сюда динамически -->
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const openModalBtn = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');
        const modal = document.getElementById('modal');
        const taskForm = document.getElementById('taskForm');
        const taskList = document.getElementById('tasks');

        openModalBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        taskForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(taskForm);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('http://localhost/FL-Tasks/TaskManager/public/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                console.log(response);
                if (!response.ok) {
                    throw new Error('Не удалось добваить задачу');
                }

                taskForm.reset();
                modal.classList.add('hidden');
                fetchTasks();
            } catch (error) {
                console.error(error);
            }
        });

        const fetchTasks = async () => {
            try {
                const response = await fetch('http://localhost/FL-Tasks/TaskManager/public/api/tasks', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                });
                if (!response.ok) {
                    throw new Error('Не удалось загрузить задач');
                }
                const tasks = await response.json();
                taskList.innerHTML = '';
                tasks.forEach(task => {
                    const taskItem = document.createElement('li');
                    taskItem.classList.add('p-4', 'border', 'border-gray-300', 'mb-2',
                        'rounded-lg', 'hover:bg-gray-100', 'transition', 'duration-300',
                        'ease-in-out');
                    taskItem.innerHTML = `<div class="flex justify-between items-center">
                                                  <div>
                                                      <strong class="text-lg">${task.title}</strong>
                                                      <p class="text-sm text-gray-600">Срок: ${task.deadline}</p>
                                                  </div>
                                                  <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded-full shadow-lg transform hover:scale-105 transition duration-300 ease-in-out delete-btn" data-id="${task.id}">Удалить</button>
                                              </div>`;
                    taskList.appendChild(taskItem);
                });

                // Добавление прослушивателей событий для кнопок удаления
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', async () => {
                        try {
                            const taskId = btn.getAttribute('data-id');
                            const deleteResponse = await fetch(
                                `http://localhost/FL-Tasks/TaskManager/public/api/tasks/${taskId}`, {
                                    method: 'DELETE',
                                });


                            if (!deleteResponse.ok) {
                                throw new Error('Не удалось удалить задачу');
                            }

                            fetchTasks();
                        } catch (error) {
                            console.error(error);
                        }
                    });
                });
            } catch (error) {
                console.error(error);
            }
        };

        // Первоначальная выборка задач
        fetchTasks();
    });
</script>
</body>

</html>
