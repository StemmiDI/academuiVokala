// import { Calendar, type Options } from "vanilla-calendar-pro";

// import "vanilla-calendar-pro/styles/index.css";

// const options: Options = {
//   type: "default",
// };

// const calendar = new Calendar("#calendar", options);
// calendar.init();
document.addEventListener("DOMContentLoaded", () => {
  // Деструктуризация конструктора Calendar
  const { Calendar } = window.VanillaCalendarPro;
  // Создайте экземпляр календаря и инициализируйте его.
  const options = {
    locale: "ru-RU",
    // layouts: {
    //   default: `
    // <h3 class="calendar-month">
    //     <#Month />
    //     <#Year />
    // </h3>
    // <div class="calendar-controls">
    //     <#ArrowPrev />
    //     <#ArrowNext />
    // </div> `,
    // },
    // TODO: https://vanilla-calendar.pro/ru/docs/learn/additional-features-layouts
  };
  const calendar = new Calendar("#calendar", options);
  calendar.init();
});
