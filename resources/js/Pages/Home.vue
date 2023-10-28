<template>

    <MainLayout>

      <search-component @get-match-events="submitSearchForm" />

      <div>

        <div v-if="searchResults.length" class="font-bold mb-4 mt-20 text-black">
          <div class="events-grid">
            <div v-for="searchResult in searchResults" :key="searchResult.id" class="event-container p-8 mb-8 bg-gray-800 text-white">

              <h2 class="text-2xl font-bold mb-4">{{ searchResult.titleEvent }}</h2>
              <p class="text-blue-300 mb-4">{{ formatTimestamp(searchResult.dateEvent) }}</p>
              <div v-html="formatDescription(searchResult.descriptionEvent)" class="text-gray-400 leading-8 mb-8"></div>

              <div class="flex space-x-4">
                <button @click="deleteConfirmation(searchResult.id)" class="bg-red-500 text-white px-4 py-2 rounded">Supprimer</button>
                <button @click="openEditModal(searchResult.id)" class="bg-green-500 text-white px-4 py-2 rounded">Modifier</button>
              </div>
            </div>
          </div>
        </div>

        <div class="font-bold mb-4 mt-20 text-black" v-else>

          <div class="events-grid">
            <div v-for="event in events.data" :key="event.id" class="event-container bg-gray-800 text-white">
              <h2 class="text-2xl font-bold mb-4">{{ event.titleEvent }}</h2>
              <p class="text-gray-300 mb-4">{{ formatTimestamp(event.dateEvent) }}</p>
              <div v-html="formatDescription(event.descriptionEvent)" class="text-gray-400 leading-8 mb-8"></div>

              <div class="flex space-x-4 ">
                <button @click="deleteConfirmation(event.id)" class="bg-red-500 text-white px-4 py-2 bottom-0 rounded">Supprimer</button>
                <button @click="openEditModal(event.id)" class="bg-green-500 text-white px-4 py-2 bottom-0 rounded">Modifier</button>
              </div>
            </div>
          </div>

          <!-- /pagination -->
          <div class="flex items-center justify-center mt-4">
            <template v-if="events.prev_page_url">
              <a
                :href="events.prev_page_url"
                :class="{ 'bg-blue-800': events.current_page === 1, 'bg-blue-500': events.current_page !== 1 }"
                class="px-4 py-2 text-white mr-2"
              >
                Précédant
              </a>
            </template>

            <template v-for="i in events.last_page" :key="i">
              <a
                :href="`${events.path}?page=${i}`"
                :class="{ 'bg-blue-800': i === events.current_page, 'bg-blue-500': i !== events.current_page }"
                class="px-4 py-2 text-white mr-2"
              >
                {{ i }}
              </a>
            </template>

            <template v-if="events.next_page_url">
              <a :href="events.next_page_url" class="px-4 py-2 bg-blue-500 text-white mr-2">Suivant</a>
            </template>
          </div>

        </div>

      </div>

      <button @click="openAddModal" class="fixed bottom-0 left-0 mb-4 ml-4 bg-blue-500 text-white px-4 py-2 rounded">
        <font-awesome-icon icon="plus" class="mr-2 " />Ajouter un nouvel Evenement
      </button>

      <!-- Ajoutez la logique pour afficher le modal  de l'ajout des events-->
      <add-event-modal :show-add-modal="addModalVisible" @add-modal-closed="addModalClose" />

      <!-- Ajoutez la logique pour afficher le modal de la modification des events -->
      <edit-event-modal :show="showEditModal" @edit-modal-closed="editModalClosed" :event-id="editingElementId" />

    </MainLayout>

  </template>







<script>
export const formatTimestamp = (timestamp) => {
//utiliser le librarie Momentjs
const date = moment(timestamp);
const formattedDate = date.format('DD-MM-YYYY HH:mm:ss');
return formattedDate;
};
</script>

<script setup>

// imports component
import AddEventModal from '@/Components/Modals/addEventModal.vue'
import EditEventModal from '@/Components/Modals/editEventModal.vue'
import SearchComponent from '@/Components/searchComponent.vue'
import MainLayout from '@/Layouts/MainLayout.vue';

// imports variable exported
import { matchingEvents } from '@/Components/searchComponent.vue';

// import dependencies and library
import moment from 'moment';
import { ref, onMounted, onBeforeUnmount, watchEffect } from 'vue';

const props = defineProps({
  events: Object
});

const searchResults = ref(JSON.parse(sessionStorage.getItem('searchResults')) || []);
const events = props.events;
const addModalVisible = ref(false);

const openAddModal = () => {
  addModalVisible.value = true;
};

const submitSearchForm = () => {
  searchResults.value = matchingEvents.value;
  sessionStorage.setItem('searchResults', JSON.stringify(searchResults.value));
};

// Stocke la valeur dans l'état global lors du rechargement de la page
onMounted(() => {
  if (searchResults.value.length) {
    searchResults.value = JSON.parse(sessionStorage.searchResults);
  }
});

// Supprime la valeur lorsqu'on quitte complètement la page
onBeforeUnmount(() => {
  sessionStorage.searchResults = JSON.stringify(searchResults.value);
});

// Écoute les changements de la valeur et met à jour l'état global
watchEffect(() => {
  sessionStorage.searchResults = JSON.stringify(searchResults.value);
});

const addModalClose = () => {
  addModalVisible.value = false;
};

const formatDescription = (description) => {
  // La fonction nl2br peut être implémentée en ajoutant des balises <br> après chaque saut de ligne
  return description.replace(/\n/g, '<br>');
};

// EDIT MODAL
const showEditModal = ref(false);
const editingElementId = ref(0);

// fonction pour modifier un event
const openEditModal = (id) => {
  // Implémente la logique pour modifier un événement
  showEditModal.value = true;
  editingElementId.value = id;
};

const editModalClosed = () => {
  editingElementId.value = 0;
  showEditModal.value = false;
};

// DELETE AN EVENT
const deleteEvent = async (id) => {
  let url = route('event.delete', { event: id });
  await axios.delete(url).then(response => {
    if (response.data.code == 200) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: response.data.message,
        showConfirmButton: false,
        timer: 4000,
      });
      setTimeout(() => {
        if (searchResults.value) {
          searchResults.value = searchResults.value.filter(item => item.id !== id);
          sessionStorage.setItem('searchResults', JSON.stringify(searchResults.value));
        }
        window.location.reload()
      }, 2000);
    }
  }).catch(error => {
    console.log(error);
  });
};

const deleteConfirmation = async (id) => {
  Swal.fire({
    title: 'Voulez vous vraiment supprimer cet Evenement?',
    showDenyButton: true,
    showCancelButton: false,
    confirmButtonText: 'Oui',
    denyButtonText: `Non`,
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
      deleteEvent(id);
      const originalUrl = route("events.list"); // Mettez ici l'URL d'origine de votre application
      window.history.replaceState({}, null, originalUrl);
    } else if (result.isDenied) {
      return;
    }
  });
}

</script>




<style scoped>

.events-grid {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 20px;

}

.event-container {
  padding: 20px;
  margin-bottom: 100px;
  border-radius: 10px;
  max-width: 800px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out;
}

.event-container:hover {
  transform: scale(1.05);
}











</style>

