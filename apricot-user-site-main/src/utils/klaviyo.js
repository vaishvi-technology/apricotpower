export const trackKlaviyoEvent = (eventName, eventData = {}) => {
  window.klaviyo = window.klaviyo || [];
  window.klaviyo.push(["track", eventName, eventData]);
};
