export const subscriptionFrequencies = [
  { freqType: "", name: "Select Delivery Interval", freq: "" },
  // { freqType: "w", name: "1 week", freq: "1" },
  // { freqType: "w", name: "2 weeks", freq: "2" },
  // { freqType: "w", name: "3 weeks", freq: "3" },
  { freqType: "w", name: "4 weeks", freq: "4" },
  { freqType: "w", name: "5 weeks", freq: "5" },
  { freqType: "w", name: "6 weeks", freq: "6" },
  // { freqType: "w", name: "7 weeks", freq: "7" },
  // { freqType: "w", name: "8 weeks", freq: "8" },
  // { freqType: "w", name: "9 weeks", freq: "9" },
  // { freqType: "w", name: "10 weeks", freq: "10" },
  // { freqType: "w", name: "11 weeks", freq: "11" },
  // { freqType: "w", name: "12 weeks", freq: "12" },
  // { freqType: "w", name: "13 weeks", freq: "13" },
  // { freqType: "w", name: "14 weeks", freq: "14" },
  // { freqType: "w", name: "15 weeks", freq: "15" },
  // { freqType: "w", name: "16 weeks", freq: "16" },
  // { freqType: "w", name: "17 weeks", freq: "17" },
  // { freqType: "w", name: "18 weeks", freq: "18" },
  // { freqType: "w", name: "19 weeks", freq: "19" },
  // { freqType: "w", name: "20 weeks", freq: "20" },
  // { freqType: "w", name: "21 weeks", freq: "21" },
  // { freqType: "w", name: "22 weeks", freq: "22" },
  // { freqType: "w", name: "23 weeks", freq: "23" },
  // { freqType: "w", name: "24 weeks", freq: "24" },
  { freqType: "m", name: "1 month", freq: "1" },
  { freqType: "m", name: "2 months", freq: "2" },
  { freqType: "m", name: "3 months", freq: "3" },
  { freqType: "m", name: "4 months", freq: "4" },
  { freqType: "m", name: "5 months", freq: "5" },
  { freqType: "m", name: "6 months", freq: "6" },
  { freqType: "m", name: "7 months", freq: "7" },
  { freqType: "m", name: "8 months", freq: "8" },
];
export const getPaymentOptions = (data) => {
  const paymentOptions = [
    {
      label: "Credit/Debit Card",
      value: "credit_card",
      onChangeExtra: {
        name: "new_credit_card",
        value: true,
      },
    },
    {
      label: "Digital Wallet",
      value: "paypal",
      onChangeExtra: {
        name: "new_credit_card",
        value: false,
      },
    },
    {
      label: "Check/Money Order",
      value: "cmo",
      onChangeExtra: {
        name: "cmo",
        value: true,
      },
    },
    {
      label: "",
      value: "coinbase",
      onChangeExtra: {
        name: "coinbase",
        value: true,
      },
    },
    // Only show credits option if account has credits
    ...(data?.amount > 0
      ? [
          {
            label: `Apply Credits before payment : ($${data?.amount} on account)`,
            value: "credits",
            checkbox: true,
            onChangeExtra: {
              name: "credits",
              value: true,
            },
          },
        ]
      : []),
    {
      label: `Apply Net 30`,
      checkbox: true,
      value: "net_thirty",
      onChangeExtra: {
        name: "net_thirty",
        value: true,
      },
    },
  ];

  return paymentOptions;
};

export function detectCardType(cardNumber) {
  const cleanCardNumber = cardNumber?.replace(/\D/g, "");

  const patterns = {
    visa: /^4\d{0,15}$/, // Visa: Starts with 4, up to 16 digits
    mastercard: /^5[1-5]\d{0,14}$/, // MasterCard: Starts with 51-55, up to 16 digits
    amex: /^3[47]\d{0,13}$/, // American Express: Starts with 34 or 37, up to 15 digits
    discover: /^6(?:011|5[0-9]{2}|22[1-9]|2[3-9][0-9]|[3-9][0-9]{2})\d{0,12}$/, // Discover
    unionpay: /^62\d{0,15}$/, // UnionPay: Starts with 62, up to 16-19 digits
    jcb: /^35\d{0,14}$/, // JCB: Starts with 35, up to 16 digits
    dinersclub: /^3[6-9]\d{0,12}$/, // Diners Club: Starts with 36, 38, or 39, up to 16 digits
    maestro: /^(50|56|58|6)\d{0,14}$/, // Maestro: Starts with 50, 56-58, or 6, up to 16 digits
    visaElectron: /^(4026|4175|4508|4844|4913|4917)\d{0,12}$/, // Visa Electron
    ruPay: /^(60|65|81|82|91)\d{0,12}$/, // RuPay: Starts with 60, 65, 81, 82, or 91, up to 16 digits
    solo: /^(6334|6767)\d{0,12}$/, // Solo: Starts with 6334 or 6767, up to 16 digits
    laser: /^(6304|6706|6771|6709)\d{0,12}$/, // Laser: Starts with 6304, 6706, 6771, or 6709, up to 16 digits
    elo: /^(4011|4312|4389|4514|4576|5067)\d{0,12}$/, // Elo: Starts with 4011, 4312, 4389, etc.
  };

  for (let cardType in patterns) {
    if (patterns[cardType].test(cleanCardNumber)) {
      const maxLength = cardType === "Amex" ? 18 : 19;
      return {
        name: cardType.charAt(0).toUpperCase() + cardType.slice(1),
        maxLength,
        formattedName: `${
          cardType.charAt(0).toUpperCase() + cardType.slice(1)
        } Card`,
      };
    }
  }

  return { name: "Unknown", maxLength: 16, formattedName: "Unknown Card" };
}
