<template>
    <div class="justify-center mt-0 mb-10 bg-gray-200 calendar-container">
      <div v-for="(month, index) in currentDate" :key="index" class="month">
        <div class="header text-2xl font-bold mb-4 text-black" v-if="index > 0">
          <button @click="prevMonth(index)" :disabled="isCurrentMonth(index)">&lt;</button>
          <h2>{{ getCurrentMonth(index).value }} {{ getCurrentMonth(index).year }}</h2>
          <button @click="nextMonth(index)">></button>
        </div>
        <div class="headerMonthOne text-2xl font-bold mb-4 text-black" v-else>
          <h2>{{ getCurrentMonth(index).value }} {{ getCurrentMonth(index).year }}</h2>
        </div>

        <div class="days">
          <div v-for="nameDay in nameDays" :key="nameDay" class="nameDays bg-blue-500">
            <div>{{ nameDay }}</div>
          </div>
          <div v-for="weekday in weekdays" :key="weekday" class="weekday">
            <div v-for="day in generateDaysArray(index)[weekday]" :key="day.number" class="day">
              <div v-if="day.number === 0">
                <div class="day-number-zero">{{ day.number }}</div>
              </div>
              <div v-else>
                <div
                  class="day-number"
                  @click="recuperateDay(day, index)"
                  :class="{
                    'text-gray-400 not-allowed-cursor': day.number < currentDay && currentMonth === getCurrentMonth(index).key && currentYear === getCurrentMonth(index).year,
                    'bg-black text-white':  (dayClickedDebut === day.number && isMonthClickedDebut === getCurrentMonth(index).key && yearClickedDebut === getCurrentMonth(index).year) || (dayClickedFin === day.number && isMonthClickedFin === getCurrentMonth(index).key && yearClickedFin === getCurrentMonth(index).year),
                  }"
                >
                  {{ day.number }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>







<script>
export const dateInterval = ref(JSON.parse(sessionStorage.getItem('dateInterval')) || "");
</script>




<script setup>
import { ref,onMounted, onBeforeUnmount, watchEffect } from 'vue';




const nameDays = ['DI', 'LU', 'MA', 'ME', 'JE', 'VE', 'SA' ];
const dateDebut = ref("")
const dateFin = ref("")

const isMonthClickedDebut = ref(JSON.parse(sessionStorage.getItem('isMonthClickedDebut')) || null);
 const isMonthClickedFin = ref(JSON.parse(sessionStorage.getItem('isMonthClickedFin')) || null);
 const dayClickedDebut = ref(JSON.parse(sessionStorage.getItem('dayClickedDebut')) || null);
 const dayClickedFin = ref(JSON.parse(sessionStorage.getItem('dayClickedFin')) || null);
 const yearClickedDebut = ref(JSON.parse(sessionStorage.getItem('yearClickedDebut')) || null);
 const yearClickedFin = ref(JSON.parse(sessionStorage.getItem('yearClickedFin')) || null);
 let dayDebut = JSON.parse(sessionStorage.getItem('dayDebut')) || 0;
 let dayFin = JSON.parse(sessionStorage.getItem('dayFin')) || 0;
 let monthDebut = JSON.parse(sessionStorage.getItem('monthDebut')) || 0;
 let monthFin = JSON.parse(sessionStorage.getItem('monthFin')) || 0;
 let yearDebut = JSON.parse(sessionStorage.getItem('yearDebut')) || 0;
 let yearFin = JSON.parse(sessionStorage.getItem('yearFin')) || 0;



// Stocke la valeur dans l'état global lors du rechargement de la page
onMounted(() => {

if(dateInterval.value.length){
dateInterval.value = JSON.parse(sessionStorage.dateInterval);
isMonthClickedDebut.value = JSON.parse(sessionStorage.getItem('isMonthClickedDebut'))
isMonthClickedFin.value = JSON.parse(sessionStorage.getItem('isMonthClickedFin'))
dayClickedDebut.value = JSON.parse(sessionStorage.getItem('dayClickedDebut'))
dayClickedFin.value = JSON.parse(sessionStorage.getItem('dayClickedFin'))
yearClickedDebut.value = JSON.parse(sessionStorage.getItem('yearClickedDebut'))
yearClickedFin.value = JSON.parse(sessionStorage.getItem('yearClickedFin'))
dayDebut = JSON.parse(sessionStorage.getItem('dayDebut'))
dayFin = JSON.parse(sessionStorage.getItem('dayFin'))
monthDebut = JSON.parse(sessionStorage.getItem('monthDebut'))
monthFin = JSON.parse(sessionStorage.getItem('monthFin'))
yearDebut = JSON.parse(sessionStorage.getItem('yearDebut'))
yearFin = JSON.parse(sessionStorage.getItem('yearFin'))
}

});



// Supprime la valeur lorsqu'on quitte complètement la page
onBeforeUnmount(() => {

sessionStorage.dateInterval = JSON.stringify(dateInterval.value);
sessionStorage.isMonthClickedDebut = JSON.stringify(isMonthClickedDebut.value);
sessionStorage.isMonthClickedFin = JSON.stringify(isMonthClickedFin.value);
sessionStorage.dayClickedDebut = JSON.stringify(dayClickedDebut.value);
sessionStorage.dayClickedFin = JSON.stringify(dayClickedFin.value);
sessionStorage.yearClickedDebut = JSON.stringify(yearClickedDebut.value);
sessionStorage.yearClickedFin = JSON.stringify(yearClickedFin.value);
sessionStorage.dayDebut = JSON.stringify(dayDebut);
sessionStorage.dayFin = JSON.stringify(dayFin);
sessionStorage.monthDebut = JSON.stringify(monthDebut);
sessionStorage.monthFin = JSON.stringify(monthFin);
sessionStorage.yearDebut = JSON.stringify(yearDebut);
sessionStorage.yearFin = JSON.stringify(yearFin);

});


// Écoute les changements de la valeur et met à jour l'état global
watchEffect(() => {

sessionStorage.dateInterval = JSON.stringify(dateInterval.value);
sessionStorage.isMonthClickedDebut = JSON.stringify(isMonthClickedDebut.value);
sessionStorage.isMonthClickedFin = JSON.stringify(isMonthClickedFin.value);
sessionStorage.dayClickedDebut = JSON.stringify(dayClickedDebut.value);
sessionStorage.dayClickedFin = JSON.stringify(dayClickedFin.value);
sessionStorage.yearClickedDebut = JSON.stringify(yearClickedDebut.value);
sessionStorage.yearClickedFin = JSON.stringify(yearClickedFin.value);
sessionStorage.dayDebut = JSON.stringify(dayDebut);
sessionStorage.dayFin = JSON.stringify(dayFin);
sessionStorage.monthDebut = JSON.stringify(monthDebut);
sessionStorage.monthFin = JSON.stringify(monthFin);
sessionStorage.yearDebut = JSON.stringify(yearDebut);
sessionStorage.yearFin = JSON.stringify(yearFin);

});


const getNextMonth = (date) => {
  const nextMonthDate = new Date(date.getFullYear(), date.getMonth() + 1, 1);
  return nextMonthDate;
};

const getPreviousMonth = (date) => {
  const previousMonthDate = new Date(date.getFullYear(), date.getMonth() - 1, 1);
  return previousMonthDate;
};


const getCurrentMonth = (index) => {
  const months = {
    1: 'January', 2: 'February', 3: 'March', 4: 'April',
    5: 'May', 6: 'June', 7: 'July', 8: 'August',
    9: 'September', 10: 'October', 11: 'November', 12: 'December',
};



const monthIndex = currentDate.value[(index)].getMonth();
const year = currentDate.value[(index)].getFullYear();


const result = {
key: monthIndex + 1, // Ajout de 1 car les mois sont indexés de 0 à 11
value: months[monthIndex+1],
year: year,
};

return result;
};


const currentDate = ref([new Date(), getNextMonth(new Date())]);
const currentDay = currentDate.value[0].getDate();
const currentMonth = currentDate.value[0].getMonth() + 1;
const currentYear =  currentDate.value[0].getFullYear();
const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];


const generateDaysArray = (index) => {
  const firstDayOfMonth = new Date(currentDate.value[index].getFullYear(), currentDate.value[index].getMonth(), 1);
  const daysInMonth = new Date(currentDate.value[index].getFullYear(), currentDate.value[index].getMonth() + 1, 0).getDate();
  const startOffset = firstDayOfMonth.getDay();
  const daysArray = {};

  for (let day = 1; day <= daysInMonth; day++) {
    const dayOfWeek = (startOffset + day - 1) % 7;
    const dayName = weekdays[dayOfWeek];
    if (!daysArray[dayName]) {
      daysArray[dayName] = [];
    }
    daysArray[dayName].push({ number: day, name: dayName });
  }

  if (daysArray.Tue.find(day => day.number === 1 && day.name === 'Tue')) {
  daysArray.Mon.unshift({ number: 0, name: 'Mon' });
  daysArray.Sun.unshift({ number: 0, name: 'Sun' });
}

if (daysArray.Mon.find(day => day.number === 1 && day.name === 'Mon')) {
  daysArray.Sun.unshift({ number: 0, name: 'Sun' });
}

if (daysArray.Wed.find(day => day.number === 1 && day.name === 'Wed')) {
  daysArray.Tue.unshift({ number: 0, name: 'Tue' });
  daysArray.Mon.unshift({ number: 0, name: 'Mon' });
  daysArray.Sun.unshift({ number: 0, name: 'Sun' });
}

if (daysArray.Thu.find(day => day.number === 1 && day.name === 'Thu')) {
  daysArray.Tue.unshift({ number: 0, name: 'Tue' });
  daysArray.Mon.unshift({ number: 0, name: 'Mon' });
  daysArray.Sun.unshift({ number: 0, name: 'Sun' });
  daysArray.Wed.unshift({ number: 0, name: 'Wed' });
}

if (daysArray.Fri.find(day => day.number === 1 && day.name === 'Fri')) {
  daysArray.Tue.unshift({ number: 0, name: 'Tue' });
  daysArray.Mon.unshift({ number: 0, name: 'Mon' });
  daysArray.Sun.unshift({ number: 0, name: 'Sun' });
  daysArray.Wed.unshift({ number: 0, name: 'Wed' });
  daysArray.Thu.unshift({ number: 0, name: 'Thu' });
}

if (daysArray.Sat.find(day => day.number === 1 && day.name === 'Sat')) {
  daysArray.Tue.unshift({ number: 0, name: 'Tue' });
  daysArray.Mon.unshift({ number: 0, name: 'Mon' });
  daysArray.Sun.unshift({ number: 0, name: 'Sun' });
  daysArray.Wed.unshift({ number: 0, name: 'Wed' });
  daysArray.Thu.unshift({ number: 0, name: 'Thu' });
  daysArray.Fri.unshift({ number: 0, name: 'Fri' });
}

  return daysArray;
};



const nextMonth = (index) => {
  currentDate.value[1] = getNextMonth(currentDate.value[1]);

};


const prevMonth = (index) => {
  currentDate.value[1] = getPreviousMonth(currentDate.value[1]);
};


const isCurrentMonth = (index) => {
  const today = new Date();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();
  const displayedMonth = currentDate.value[index].getMonth();
  const displayedYear = currentDate.value[index].getFullYear();

  return currentMonth === displayedMonth && currentYear === displayedYear;
};



const recuperateDay = (day, index) => {


    if((dayDebut === 0 && getCurrentMonth(index).year !== currentYear) ||
         ( dayDebut === 0 && getCurrentMonth(index).key === currentMonth && day.number >= currentDay && getCurrentMonth(index).year === currentYear ) ||
         dayDebut === 0 && getCurrentMonth(index).key !== currentMonth ){
         isMonthClickedDebut.value = getCurrentMonth(index).key
         dayClickedDebut.value = day.number
         yearClickedDebut.value = getCurrentMonth(index).year

        dayDebut = day.number
        monthDebut = getCurrentMonth(index).key
        yearDebut = getCurrentMonth(index).year
    }else{
        if(day.number === dayDebut && getCurrentMonth(index).key === monthDebut && getCurrentMonth(index).year === yearDebut ){

            isMonthClickedDebut.value = ""
            yearClickedDebut.value = ""
            dayClickedDebut.value = ""
            dayDebut = 0
            monthDebut = 0
            yearDebut = 0

        }
        else{
        if(((dayDebut !== 0 && getCurrentMonth(index).year !== currentYear) ||
         ( dayDebut !== 0 && getCurrentMonth(index).key === currentMonth && day.number >= currentDay && getCurrentMonth(index).year === currentYear ) ||
         (dayDebut !== 0 && getCurrentMonth(index).key !== currentMonth)) )
         {
            if(dayFin !== day.number){
            dayClickedFin.value = day.number
            yearClickedFin.value = getCurrentMonth(index).year
            isMonthClickedFin.value = getCurrentMonth(index).key
            dayFin = day.number
            monthFin = getCurrentMonth(index).key
            yearFin = getCurrentMonth(index).year

            }else{
            if(day.number === dayFin && getCurrentMonth(index).key === monthFin && getCurrentMonth(index).year === yearFin ){

            isMonthClickedFin.value = ""
            yearClickedFin.value = ""
            dayClickedFin.value = ""
            dayFin = 0
            monthFin = 0
            yearFin = 0

            }
        }

        }

    }
    }


if(dayDebut !== 0 && monthDebut !==0 && yearDebut !==0){

dateDebut.value = dayDebut + "/" + monthDebut + "/" + yearDebut
dateFin.value = dayFin + "/" + monthFin + "/" + yearFin

}else{
    dateDebut.value=" .....  ";
}

if(dateDebut.value !== '0/0/0' && dateFin.value === '0/0/0'){
dateInterval.value = dateDebut.value + " Au " + "......"
}else{
if(dateDebut.value!== '0/0/0' && dateFin.value !== '0/0/0'){
    dateInterval.value = dateDebut.value + " Au " + dateFin.value
}
}


}




</script>



  <style scoped>
  .calendar-container {
   max-width: 800px;
   display: flex;


  }

  .month {
    margin-right: 5px;
  }

  .header {
    display: flex;
    justify-content: space-around;
    align-items: center;
    margin-bottom: 30px;
    padding-top: 50px;
  }

  .headerMonthOne {
    display: block;
    text-align: center;
    justify-content: center;
    margin-bottom: 30px;
    padding-top: 50px;
  }

  .days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0px;
    margin-left: 2px;

  }


  .weekday {
    text-align: center;
    padding: 5px;
    border: 0px solid #ccc;
    border-bottom: 0px solid #ccc;
    background-color: #f0f0f0;
  }

  .nameDays {
    text-align: center;
    padding: 15px;
    border: 1px solid #ccc;

  }

  .day {
    text-align: center;


  }

  .day-number {
    font-weight: bold;
    padding: 10px;
    cursor: pointer;
  }

  .day-number-zero {
    font-weight: bold;
    padding: 10px;
    color: #f0f0f0;
  }

  .day-number:hover {
    border: 1px solid black;
  }



  button:disabled {
    color: #999;
    cursor: not-allowed;
  }

  .not-allowed-cursor{
    cursor: not-allowed;
  }






  @media (max-width: 820px) {
     .calendar-container{
        max-width: 500px;
   display: block
     }
    }
  </style>
