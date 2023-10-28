<template>
    <div v-show="isModalOpen" class="fixed inset-0 flex items-center bg-black bg-opacity-80 justify-center">
      <div class="bg-white p-8 rounded w-[500px]">
        <div @click="closeEditModal" class="cursor-pointer text-gray-600 hover:text-gray-800 float-right">
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

        <h2 class="text-2xl font-bold mb-4">Modification d'un Evenement</h2>

        <!-- Formulaire -->
        <form @submit.prevent="submitEditForm">
          <div class="mb-4">
            <label for="titleEvent" class="block text-sm font-medium text-gray-600">Titre de l'événement</label>
            <input v-model="editEvent.titleEvent" type="text" autocomplete="off" placeholder="Ajouter le titre de l'evenement" id="titleEvent" name="titleEvent" class="mt-1 p-2 w-full border rounded-md">
          </div>

          <div class="mb-4">
            <label for="descriptionEvent" class="block text-sm font-medium text-gray-600">Description de l'événement</label>
            <textarea v-model="editEvent.descriptionEvent" id="descriptionEvent" autocomplete="off" name="descriptionEvent" rows="4" class="mt-1 p-2 w-full border rounded-md"></textarea>
          </div>

          <div class="flex">
            <div class="mb-4 left-0 w-[250px]">
              <label for="editDateInput" class="block text-sm font-medium text-gray-600">Date de déroulement</label>
              <input v-model="editDateInput" type="text" autocomplete="off" id="editDateInput" placeholder="JJ/MM/ANNEE" name="editDateInput" class="mt-1 p-2 w-full border rounded-md" @input="formatEditDateInput" />
            </div>

            <div class="mb-4 right-0 ml-12 w-[250px]">
              <label for="editHourInput" class="block text-sm font-medium text-gray-600">Heure de déroulement</label>
              <input v-model="editHourInput" autocomplete="off" type="text" id="editHourInput" name="editHourInput" placeholder="HH:MIN:SEC" class="mt-1 p-2 w-full border rounded-md" @input="formatEditHourInput" />
            </div>
          </div>

          <div class="flex justify-end">
            <div class="flex items-center">
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Valider</button>
            </div>

            <div class="flex items-center ml-2">
              <button type="reset" @click="closeEditModal" class="bg-red-500 text-white px-4 py-2 rounded">Fermer</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </template>









<script setup>

// import dependencies and library
import axios from 'axios';
import { ref,defineProps, defineEmits, watch } from 'vue';
import moment from 'moment';

//import const
import { formatTimestamp } from '@/Pages/Home.vue';


const emit = defineEmits(["editModalClosed"]);

const props = defineProps({
    eventId: {
        type: Number,
        required: true
    },

    show: {
        type: Boolean,
        default: false
    }
})


const editEvent = ref({})

const isModalOpen = ref(false);
const errors = ref([]);
const editDateInput = ref('')
const editHourInput = ref('')

const openModal = () => {
    isModalOpen.value = true;
    getEventById()


};

const closeEditModal = () => {
    isModalOpen.value = false;
    emit("editModalClosed")


};

const getEventById = ()=>{
const id = props.eventId
    axios.get(route("event.get", { event: id}))

    .then((response)=>{
        editEvent.value.id = response.data.event.id
        editEvent.value.titleEvent = response.data.event.titleEvent
        editEvent.value.slugEvent = response.data.event.slugEvent
        editEvent.value.dateEvent = response.data.event.dateEvent
        editEvent.value.descriptionEvent = response.data.event.descriptionEvent
        editDateInput.value = formatTimestamp(response.data.event.dateEvent).slice(0, 10).replace(/-/g, '/');
        editHourInput.value = formatTimestamp(response.data.event.dateEvent).slice(11, 19).split(':').map((time, index) => `${time}${index === 0 ? 'h' : index === 1 ? 'min' : 'sec'}`).join(':');


    },

    )

    .catch((error)=>{

        console.log(error)

    })
}


watch(

    () => props.show,
    (newVal, oldVal)=>{

    if(newVal){
        openModal()
    }else{
        closeEditModal()
    }

    }

)


const submitEditForm = async () => {

errors.value = [];

validateForm();


if (!errors.value.length) {
const formattedDateInput = formatAndCleanDate();

const formData = new FormData();
formData.append('titleEvent', editEvent.value.titleEvent);
formData.append('descriptionEvent', editEvent.value.descriptionEvent);
formData.append('dateEvent', formattedDateInput);

try {
    const response = await axios.post(route('event.update', {event: editEvent.value.id}), formData);

    handleResponse(response);


// Récupérer les données actuelles de sessionStorage
var dataArray = JSON.parse(sessionStorage.getItem('searchResults'));

// Vérifier si le tableau existe
if (Array.isArray(dataArray)) {
// Rechercher l'objet avec l'id 2
var searchResult = dataArray.find(obj => obj.id === editEvent.value.id)

// Vérifier si l'objet a été trouvé
if (searchResult) {
// Modifier la propriété que vous souhaitez
searchResult.titleEvent = editEvent.value.titleEvent
    searchResult.descriptionEvent = editEvent.value.descriptionEvent
    searchResult.dateEvent = formattedDateInput

// Mettre à jour les données dans sessionStorage
sessionStorage.setItem('searchResults', JSON.stringify(dataArray));

} else {
console.log("L'objet avec l'id 2 n'a pas été trouvé dans le tableau.");
}
} else {
console.log("Le tableau n'a pas été trouvé dans sessionStorage.");
}




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



//nettoyer la date et l'heur pour obtenir jj/mm/yyyyhh:mm:ss
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
if (!editEvent.value.titleEvent) {
errors.value.push("Le titre est obligatoire");
}

if (!editEvent.value.descriptionEvent) {
errors.value.push("La description est obligatoire");
}

if(!isValidDateTimeForLeapYear(editDateInput.value + editHourInput.value)){
errors.value.push("La date saisie est incorrecte ou est deja passée");
}

if (!editDateInput.value) {
errors.value.push("La date est obligatoire");
}

if (!editHourInput.value) {
errors.value.push("L'heure est obligatoire");
}
};


//Obtenir le format YYYY-MM-DDTHH:mm:ss.SSSSSSZ
const formatAndCleanDate = () => {
// j'ai separer les 2 par "à" pour rendre la fonction nettoyerDate() moins complexe
const dateAndHour = editDateInput.value + "à" + editHourInput.value;
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

closeEditModal();
} else if (code === 400) {
Swal.fire({
    ...toastConfig,
    icon: 'error',
    title: message,
    timer: 5000,
});
}else if(code === 422){
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
const formatEditDateInput = () => {
    if (editDateInput.value.length === 2 || editDateInput.value.length === 5) {
    editDateInput.value += "/";
}
}

// Formatage de la date et de l'heure avec les tabulations automatiques
const formatEditHourInput = () => {
    if (editHourInput.value.length === 2) {
    editHourInput.value += "h:";
} else if (editHourInput.value.length === 12) {
    editHourInput.value += "sec";
} else if (editHourInput.value.length === 6) {
    editHourInput.value += "min:";
}

};

window.addEventListener('keydown', (event) => {
    // Récupère l'élément actuellement ciblé
    const activeElement = document.activeElement;


//TRAITEMENT SUR LE CHAMP HEURE DE L'EVENEMENT
if (activeElement.id === 'editHourInput') {

    // Empêcher la saisie d'un caractère qui n'est pas un chiffre
    if (!/^\d$/.test(event.key) && event.key !== "Enter" && event.key !== "Backspace") {
    event.preventDefault();
    }

    //l'heure ne peut commencer par 3 ou plus
    if (editHourInput.value.length === 0 && event.key !== "0" && event.key !== "2" && event.key !== "1") {
    event.preventDefault();
    }


    //l'heur ne peux pas depasser les 23h
    if (editHourInput.value.length === 1 && editHourInput.value === "2" && event.key !== "0" && event.key !== "1" && event.key !== "2" && event.key !== "3") {
    event.preventDefault();
    }


//les minutes et les secondes sont moins de 60
    if ((editHourInput.value.length === 4 || editHourInput.value.length === 10) && !/^[0-5]$/.test(event.key)) {
    event.preventDefault();
    }

    if (event.key === 'Backspace') {
    event.preventDefault();
    // Applique ton traitement spécifique pour l'input "editHourInput"
    editHourInput.value = editHourInput.value.slice(0, -1);
    } else {
    const forbiddenLengths = [15, 2, 3, 6, 7, 8, 12, 13, 14];
    if (forbiddenLengths.includes(editHourInput.value.length) && event.key !== "Enter" && event.key !== "Backspace") {
        event.preventDefault();
    }
    }
}


//TRAITEMENT SUR LE CHAMP DATE DE L'EVENEMENT

if (activeElement.id === 'editDateInput') {
    // Empêcher la saisie d'un caractère qui n'est pas un chiffre
    if (
        (!/^\d$/.test(event.key) && event.key !== "Enter" && event.key !== "Backspace") ||
        editDateInput.value.length === 10
    ) {
        event.preventDefault();
    }

    // Le jour doit commencer par 0, 1, 2 ou 3
    if (editDateInput.value.length === 0 && !["0", "1", "2", "3"].includes(event.key)) {
        event.preventDefault();
    }


    // Les jours ne peuvent pas dépasser les 31 jours
    if (editDateInput.value.length === 1 && editDateInput.value === "3" && !["0", "1"].includes(event.key)) {
        event.preventDefault();
    }

    // Les mois doivent commencer par un 0 ou 1
    if (editDateInput.value.length === 3 && !["0", "1"].includes(event.key)) {
        event.preventDefault();
    }

    // Le mois ne peut pas dépasser 12
    if (
        editDateInput.value.length === 4 &&
        editDateInput.value[3] === "1" &&
        !["0", "1", "2"].includes(event.key)
    ) {
        event.preventDefault();
    }

    // Le mois de février ne peut pas avoir 30 ou 31 jours
    if (editDateInput.value.length === 4 && editDateInput.value[0] === "3" && editDateInput.value[3] === "0" && event.key === "2") {
        event.preventDefault();
    }

    // Les mois en 31 jours ou en 30 jours
    if (
        editDateInput.value.length === 4 &&
        editDateInput.value[0] === "3" &&
        editDateInput.value[1] === "1" &&
        ((editDateInput.value[3] === "0" && ["4", "6", "9"].includes(event.key)) ||
            (editDateInput.value[3] === "1" && event.key === "1"))
    ) {
        event.preventDefault();
    }

    // Reprogrammer la touche Backspace pour qu'elle fonctionne comme on veut
    if (event.key === 'Backspace') {
        event.preventDefault();

        if (editDateInput.value.length === 3 || editDateInput.value.length === 6) {
            editDateInput.value = editDateInput.value.slice(0, -2);
        } else {
            editDateInput.value = editDateInput.value.slice(0, -1);
        }
    }
}

});


</script>
