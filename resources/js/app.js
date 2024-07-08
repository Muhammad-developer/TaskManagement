import './bootstrap';

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
            const response = await fetch('/api/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                throw new Error('Failed to add task');
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
            const response = await fetch('/api/tasks');
            if (!response.ok) {
                throw new Error('Failed to fetch tasks');
            }
            const tasks = await response.json();
            taskList.innerHTML = '';
            tasks.forEach(task => {
                const taskItem = document.createElement('li');
                taskItem.classList.add('p-4', 'border', 'border-gray-300', 'mb-2', 'rounded-lg', 'hover:bg-gray-100', 'transition', 'duration-300', 'ease-in-out');
                taskItem.innerHTML = `<div class="flex justify-between items-center">
                                          <div>
                                              <strong class="text-lg">${task.title}</strong>
                                              <p class="text-sm text-gray-600">Deadline: ${task.deadline}</p>
                                          </div>
                                          <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded-full shadow-lg transform hover:scale-105 transition duration-300 ease-in-out delete-btn" data-id="${task.id}">Delete</button>
                                      </div>`;
                taskList.appendChild(taskItem);
            });

            // Добавление прослушивателей событий для кнопок удаления
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', async () => {
                    try {
                        const taskId = btn.getAttribute('data-id');
                        const deleteResponse = await fetch(`/api/tasks/${taskId}`, {
                            method: 'DELETE',
                        });

                        if (!deleteResponse.ok) {
                            throw new Error('Failed to delete task');
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