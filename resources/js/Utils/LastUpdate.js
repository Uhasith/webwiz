import {format, parse} from 'date-fns';

export function formatDate(dateString, toString = true) {
    if (!dateString || dateString === null) {
        return "";
    }

    const formattedDate = (new Date(dateString)).toISOString().split('.')[0].replace('T', ' ');
    const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    const date = parse(formattedDate, 'yyyy-MM-dd HH:mm:ss', new Date());
    const utcDate = new Date(date + ' UTC');

    const localDate = utcDate.toLocaleString('en-US', {
        timeZone: timeZone, year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
    }).replace(',', '');

    if(toString){
        return format(localDate.replace(/(\d{2})\/(\d{2})\/(\d{4})/,
                '$3-$1-$2'),
            'dd MMM yyyy, hh:mm a');
    }

    return localDate;
}
export function modifyDate(date, days) {
    const newDate = new Date(date);
    newDate.setDate(newDate.getDate() + days);
    return newDate.toISOString().split('T')[0];
}
export function getHoursWithAMPM(dateArray) {
    return dateArray.map(timestamp => {
        const date = new Date(formatDate(timestamp));
        return date.toLocaleTimeString('en-US', { hour: 'numeric', hour12: true });
    });
}
export function getDaysByDate(dateArray) {
    return dateArray.map(timestamp => {
        const date = new Date(formatDate(timestamp));
        const weekday = date.toLocaleDateString('en-US', { weekday: 'long' });
        const day = date.getDate().toString().padStart(2, '0'); // Ensure two digits
        return `${weekday.substring(0,3)} ${day}`;
    });
}
function getWeekStartAndEnd(date) {
    const currentDate = new Date(date);

    // Adjust the date to the start of the week (assuming week starts on Sunday)
    const firstDayOfWeek = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()));

    // Get the end of the week (add 6 days to the first day of the week)
    const lastDayOfWeek = new Date(firstDayOfWeek);
    lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);

    return {
        start: firstDayOfWeek,
        end: lastDayOfWeek
    };
}
export function getWeeksByDate(dateArray) {
    return dateArray.map(timestamp => {
        const date = new Date(formatDate(timestamp));
        const { start, end } = getWeekStartAndEnd(date);
        return start.toLocaleDateString('en-US',{day:'2-digit',month:'numeric'})+" "+end.toLocaleDateString('en-US',{day:'2-digit',month:'numeric'});
    });
}
export function getMonthsByDate(dateArray) {
    return dateArray.map(timestamp => {
        const date = new Date(formatDate(timestamp));
        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
}
export function getYearByDate(dateArray) {
    return dateArray.map(timestamp => {
        const date = new Date(formatDate(timestamp));
        return date.toLocaleDateString('en-US', { year: 'numeric' });
    });
}
