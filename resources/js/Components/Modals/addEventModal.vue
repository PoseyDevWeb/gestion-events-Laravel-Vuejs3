<template>
    <div v-show="isAddModalOpen" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center">
      <div class="bg-white p-8 rounded w-[500px]">
        <div @click="closeAddModal" class="cursor-pointer text-gray-600 hover:text-gray-800 float-right">
          <!-- Icône pour fermer le modal (utilisez une icône de votre choix) -->
          &#x2715;
        </div>

        <div class="bg-red-500 mt-8 p-4" v-if="errors.length">
          <ul class="mb-0">
            <li v-for="(error, index) in errors" :key="index">
              {{ error }}
            </li>
          </ul>
        </div>

        <h2 class="text-2xl font-bold mb-4">Ajouter un nouvel événement</h2>

        <!-- Formulaire -->
        <form @submit.prevent="submitAddForm">
          <div class="mb-4">
            <label for="titleEvent" class="block text-sm font-medium text-gray-600">Titre de l'événement</label>
            <input v-model="event.titleEvent" autocomplete="off" type="text" placeholder="Ajouter le titre de l'evenement" id="titleEvent" name="titleEvent" class="mt-1 p-2 w-full border rounded-md">
          </div>

          <div class="mb-4">
            <label for="descriptionEvent" class="block text-sm font-medium text-gray-600">Description de l'événement</label>
            <textarea v-model="event.descriptionEvent" autocomplete="off" id="descriptionEvent" name="descriptionEvent" rows="4" class="mt-1 p-2 w-full border rounded-md"></textarea>
          </div>

          <div class="flex">
            <div class="mb-4 left-0 w-[250px]">
              <label for="addDateInput" class="block text-sm font-medium text-gray-600">Date de déroulement</label>
              <input v-model="addDateInput" autocomplete="off" type="text" id="addDateInput" placeholder="JJ/MM/ANNEE" name="addDateInput" class="mt-1 p-2 w-full border rounded-md" @input="formatAddDateInput" />
            </div>

            <div class="mb-4 right-0 ml-12 w-[250px]">
              <label for="addHourInput" class="block text-sm font-medium text-gray-600">Heure de déroulement</label>
              <input v-model="addHourInput" autocomplete="off" type="text" id="addHourInput" name="addHourInput" placeholder="HH:MIN:SEC" class="mt-1 p-2 w-full border rounded-md" @input="formatAddHourInput" />
            </div>
          </div>

          <div class="flex justify-end mt-4">
            <div class="flex items-center">
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Ajouter</button>
            </div>

            <div class="flex items-center ml-2">
              <button type="reset" @click="closeAddModal" class="bg-red-500 text-white px-4 py-2 rounded">Fermer</button>
            </div>
          </div>
        </form>
      </div>
    </div>
</template>






  <script setup>
  import { ref, defineProps, defineEmits, watch } from 'vue';
  import moment from "moment";


  // Données du formulaire
const event = ref({})
const addDateInput = ref('');
const addHourInput = ref('');
const errors = ref([]);
const isAddModalOpen = ref(false);

const props = defineProps({

    showAddModal: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(["addModalClosed"])

const openAddModal = () => {
  isAddModalOpen.value = true;
};

const closeAddModal = () => {
  isAddModalOpen.value = false;
  event.value = {};
 addDateInput.value = "";
 addHourInput.value= "";
errors.value = [];
  emit("addModalClosed")
};


watch(

  () => props.showAddModal,
  (newVal, oldVal)=>{

    if(newVal){
        openAddModal()
    }else{
        closeAddModal()
    }

  }

)




// Méthode pour soumettre le formulaire

const submitAddForm = async () => {
  errors.value = [];
  validateForm();


  if (!errors.value.length) {
    const formattedDateInput = formatAndCleanDate();

    const formData = new FormData();
    formData.append('titleEvent', event.value.titleEvent);
    formData.append('descriptionEvent', event.value.descriptionEvent);
    formData.append('dateEvent', formattedDateInput);

    try {
      const response = await axios.post(route('event.store'), formData);

      handleResponse(response);

    } catch (error) {
      console.error('An error occurred:', error);
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: "une erreur est survenu: l'operation a echoué",
        showConfirmButton: false,
        timer: 5000,

      });
    }
  }
};


function cleanerDate(dateString) {
  // Supprimer "à", "h:", "min:" et "sec"
  let cleanedString = dateString.replace("à", "").replace("h", "").replace("min", "").replace("sec", "");
  return cleanedString;
}



function isValidDateTimeForLeapYear(dateTimeString) {
// Extraire les composants de la date et de l'heure
const day = parseInt(dateTimeString.substr(0, 2), 10);
  const month = parseInt(dateTimeString.substr(3, 2), 10);
  const year = parseInt(dateTimeString.substr(6, 4), 10);
  const hours = parseInt(dateTimeString.substr(10, 2), 10);
  const minutes = parseInt(dateTimeString.substr(14, 2), 10);
  const seconds = parseInt(dateTimeString.substr(20, 2), 10);

  // Vérifier si c'est une année bissextile
  const isLeapYear = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);

  // Obtenir la date actuelle
  const currentDate = new Date();

  // Vérifier si la date et l'heure sont ultérieures à aujourd'hui
  const inputDateTime = new Date(year, month - 1, day, hours, minutes, seconds);
  return inputDateTime > currentDate && (month !== 2 || (day >= 1 && day <= (isLeapYear ? 29 : 28)));
}


//Valider les contraintes du formulaires
const validateForm = () => {
  if (!event.value.titleEvent) {
    errors.value.push("Le titre est obligatoire");
  }

  if (!event.value.descriptionEvent) {
    errors.value.push("La description est obligatoire");
  }

  if(!isValidDateTimeForLeapYear(addDateInput.value + addHourInput.value)){
    errors.value.push("La date saisie est incorrecte ou ou deja passée");
  }

  if (!addDateInput.value) {
    errors.value.push("La date est obligatoire");
  }

  if (!addHourInput.value) {
    errors.value.push("L'heure est obligatoire");
  }
};


//Obtenir le format YYYY-MM-DDTHH:mm:ss.SSSSSSZ
const formatAndCleanDate = () => {
    // j'ai separer les 2 par "à" pour rendre la fonction nettoyerDate() moins complexe
    const dateAndHour = addDateInput.value + "à" + addHourInput.value;
  return moment(cleanerDate(dateAndHour), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DDTHH:mm:ss.SSSSSSZ');
};


//gerer les reponses de la requettes
const handleResponse = (response) => {
  const { code, message } = response.data;

  const toastConfig = {
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
  };

  if (code === 200) {
    setTimeout(() => window.location.reload(), 3000);

    Swal.fire({
      ...toastConfig,
      icon: 'success',
      title: message,
      timer: 3000,
    });

    closeAddModal();
  } else if (code === 400) {
    Swal.fire({
      ...toastConfig,
      icon: 'error',
      title: message,
      timer: 5000,
    });
  } else {
    Swal.fire({
      ...toastConfig,
      icon: 'error',
      title: "Erreur Inconnue !",
      timer: 4000,
    });
  }
};



// Formatage de la date et de l'heure avec les tabulations automatiques
const formatAddDateInput = () => {
  if (addDateInput.value.length === 2 || addDateInput.value.length === 5) {
  addDateInput.value += "/";
}
}

// Formatage de la date et de l'heure avec les tabulations automatiques
const formatAddHourInput = () => {
  if (addHourInput.value.length === 2) {
  addHourInput.value += "h:";
} else if (addHourInput.value.length === 12) {
  addHourInput.value += "sec";
} else if (addHourInput.value.length === 6) {
  addHourInput.value += "min:";
}

};



window.addEventListener('keydown', (event) => {
  // Récupère l'élément actuellement ciblé
  const activeElement = document.activeElement;


 //TRAITEMENT SUR LE CHAMP HEURE DE L'EVENEMENT
if (activeElement.id === 'addHourInput') {

    // Empêcher la saisie d'un caractère qui n'est pas un chiffre
  if (!/^\d$/.test(event.key) && event.key !== "Enter" && event.key !== "Backspace") {
    event.preventDefault();
  }

  //l'heure ne peut commencer par 3 ou plus
  if (addHourInput.value.length === 0 && event.key !== "0" && event.key !== "2" && event.key !== "1") {
    event.preventDefault();
  }


  //l'heur ne peux pas depasser les 23h
  if (addHourInput.value.length === 1 && addHourInput.value === "2" && event.key !== "0" && event.key !== "1" && event.key !== "2" && event.key !== "3") {
    event.preventDefault();
  }


//les minutes et les secondes sont moins de 60
  if ((addHourInput.value.length === 4 || addHourInput.value.length === 10) && !/^[0-5]$/.test(event.key)) {
    event.preventDefault();
  }

  if (event.key === 'Backspace') {
    event.preventDefault();
    // Applique ton traitement spécifique pour l'input "addHourInput"
    addHourInput.value = addHourInput.value.slice(0, -1);
  } else {
    const forbiddenLengths = [15, 2, 3, 6, 7, 8, 12, 13, 14];
    if (forbiddenLengths.includes(addHourInput.value.length) && event.key !== "Enter" && event.key !== "Backspace") {
      event.preventDefault();
    }
  }
}


//TRAITEMENT SUR LE CHAMP DATE DE L'EVENEMENT

if (activeElement.id === 'addDateInput') {
    // Empêcher la saisie d'un caractère qui n'est pas un chiffre
    if (
        (!/^\d$/.test(event.key) && event.key !== "Enter" && event.key !== "Backspace") ||
        addDateInput.value.length === 10
    ) {
        event.preventDefault();
    }

    // Le jour doit commencer par 0, 1, 2 ou 3
    if (addDateInput.value.length === 0 && !["0", "1", "2", "3"].includes(event.key)) {
        event.preventDefault();
    }

    // Les jours ne peuvent pas dépasser les 31 jours
    if (addDateInput.value.length === 1 && addDateInput.value === "3" && !["0", "1"].includes(event.key)) {
        event.preventDefault();
    }

    // Les mois doivent commencer par un 0 ou 1
    if (addDateInput.value.length === 3 && !["0", "1"].includes(event.key)) {
        event.preventDefault();
    }

    // Le mois ne peut pas dépasser 12
    if (
        addDateInput.value.length === 4 &&
        addDateInput.value[3] === "1" &&
        !["0", "1", "2"].includes(event.key)
    ) {
        event.preventDefault();
    }

    // Le mois de février ne peut pas avoir 30 ou 31 jours
    if (addDateInput.value.length === 4 && addDateInput.value[0] === "3" && addDateInput.value[3] === "0" && event.key === "2") {
        event.preventDefault();
    }

    // Les mois en 31 jours ou en 30 jours
    if (
        addDateInput.value.length === 4 &&
        addDateInput.value[0] === "3" &&
        addDateInput.value[1] === "1" &&
        ((addDateInput.value[3] === "0" && ["4", "6", "9"].includes(event.key)) ||
            (addDateInput.value[3] === "1" && event.key === "1"))
    ) {
        event.preventDefault();
    }

    // Reprogrammer la touche Backspace pour qu'elle fonctionne comme on veut
    if (event.key === 'Backspace') {
        event.preventDefault();

        if (addDateInput.value.length === 3 || addDateInput.value.length === 6) {
            addDateInput.value = addDateInput.value.slice(0, -2);
        } else {
            addDateInput.value = addDateInput.value.slice(0, -1);
        }
    }
}

});



</script>







