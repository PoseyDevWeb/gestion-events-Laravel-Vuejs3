<template>

<div class="mb-13">
<form @submit.prevent="">
    <div class=" flex justify-center rounded ">
    <div class="flex relative bg-white">
      <div class="flex flex-col w-[400px] rounded transition duration-300"
      :class="{ 'hover:bg-gray-200': isHoveredDate, 'bg-gray-200': isFocusedDate }"
        @mouseover="isHoveredDate = true"
        @mouseleave="isHoveredDate = false"
        @focus="isFocusedDate = true"
        @blur="isFocusedDate = false"
        id="searchDateBox"
        @click="toggleDateRangePicker"
      >
    <label for="searchDate" class="pb-0 pl-6 text-center text-bold"
    >Interval de la date de l'événement
  </label>
    <input
      type="search"
      class="border-none px-3 text-center py-2 transition duration-300 focus:ring-transparent"
      :class="{ 'hover:bg-gray-200': isHoveredDate, 'bg-gray-200': isFocusedDate }"
      placeholder="Rechercher par un intervel de 2 dates"
      name="searchDate"
      id="searchDate"
      autocomplete="off"
      v-model="searchComponentInterval"
      @mouseover="isHoveredDate = true"
      @mouseleave="isHoveredDate = false"
      @focus="isFocusedDate = true"
      @blur="isFocusedDate = false"
    />
  </div>
        <button class="px-4 py-2 border-l border-gray-500 bg-blue-500 text-white " type="submit" @click="submitSearchForm" >
          Search
        </button>
      </div>

    </div>
    <date-range-picker-vue class=" mx-auto my-auto  w-[800px] " v-show="isDateRangePickerVisible" @click = "dateRangePickerVueClick" @click.stop  />
</form>


</div>
  </template>






<script>
export const matchingEvents = ref([]);
</script>


<script setup>
  // import dependencies and library
  import { ref, onBeforeUnmount, onMounted, defineEmits } from 'vue';
  import axios from 'axios';

  // import components
  import DateRangePickerVue from '@/Components/dateRangePicker.vue';
  import { dateInterval } from '@/Components/dateRangePicker.vue';

  let intervalDebut = "";
  let intervalFin = "";

  const emit = defineEmits(["getMatchEvents"]);

  const isFocused = ref(false);
  const isHoveredDate = ref(false);
  const isFocusedDate = ref(false);
  const searchComponentInterval = ref(dateInterval);
  const isDateRangePickerVisible = ref(false);

  const extraireIntervalle = () => {
    // Trouver l'index du mot "Au"
    const indexAu = searchComponentInterval.value.indexOf('Au');

    // Extraire la partie avant "Au"
    intervalDebut = searchComponentInterval.value.substring(0, indexAu).trim();

    // Extraire la partie après "Au"
    intervalFin = searchComponentInterval.value.substring(indexAu + 2).trim();

    // Remplacer '/' par '-'
    intervalDebut = intervalDebut.replace(/\//g, '-');
    intervalFin = intervalFin.replace(/\//g, '-');
  };

  function getDatesBetween(startDate, endDate) {
    // Convertir les chaînes de caractères en objets de date
    const startDateObj = new Date(startDate);
    const endDateObj = new Date(endDate);

    // Liste pour stocker les dates intermédiaires
    const allDates = [];

    // Boucle pour générer les dates intermédiaires
    let currentDate = startDateObj;
    while (currentDate <= endDateObj) {
      const formattedDate = currentDate.toISOString().split('T')[0];
      allDates.push(formattedDate);
      currentDate.setDate(currentDate.getDate() + 1);
    }

    return allDates;
  }

  const dateRangePickerVueClick = () => {
    isFocusedDate.value = true;
  };

  const toggleDateRangePicker = () => {
    isDateRangePickerVisible.value = true;
  };

  window.addEventListener('keydown', (event) => {
    // Récupère l'élément actuellement ciblé
    const activeElement = document.activeElement;

    // TRAITEMENT SUR LE CHAMP HEURE DE L'EVENEMENT
    if (activeElement.id === "searchDate") {
      // Empêcher la saisie d'un caractère qui n'est pas un chiffre
      if (event.key !== "Backspace") {
        event.preventDefault();
      }
    }
  });

  // Ajoutez un gestionnaire d'événements click au niveau global pour masquer le date-range-picker-vue
  const handleGlobalClick = (event) => {
    // Masquer le date-range-picker-vue si l'événement click n'est pas à l'intérieur de l'élément searchDate
    if (!document.getElementById('searchDateBox').contains(event.target)) {
      isDateRangePickerVisible.value = false;
      isFocusedDate.value = false;
      isFocused.value = false;
    }
  };

  window.onbeforeunload = () => {
    if (searchComponentInterval.value.length === 0) {
      sessionStorage.clear();
    }
  };

  // Ajout des gestionnaires d'événements au niveau global lors de la création du composant
  onMounted(() => {
    document.body.addEventListener('click', handleGlobalClick);
  });

  // Suppression des gestionnaires d'événements lors de la destruction du composant
  onBeforeUnmount(() => {
    document.body.removeEventListener('click', handleGlobalClick);
  });

  // Convertir les dates au format 'YYYY-MM-DD'
  const convertDate = (date) => {
    const [day, month, year] = date.split('-');
    return `${year}-${month}-${day}`;
  };

  const submitSearchForm = async () => {
    extraireIntervalle();
    intervalDebut = convertDate(intervalDebut);
    intervalFin = convertDate(intervalFin);

    if (searchComponentInterval.value.length === 0) {
      window.location.reload();
    } else {
      try {
        const response = await axios.post(route('events.search'), {
          intervallDateArray: getDatesBetween(intervalDebut, intervalFin),
        });
        matchingEvents.value = response.data.matchingEvents;
        if (matchingEvents.value.length) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: matchingEvents.value.length + " Evenement trouvés",
            showConfirmButton: false,
            timer: 6000,
          });
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          if (matchingEvents.value.length === 0) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: " Aucun Evénement ne se trouve dans cet intervalle ",
              showConfirmButton: false,
              timer: 4000,
            });
          }
        }
      } catch (error) {
        console.error('Erreur lors de la comparaison des dates', error);
      }
    }
    emit("getMatchEvents");
  };
</script>

