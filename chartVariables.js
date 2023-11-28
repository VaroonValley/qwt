//Chart Types
const mainChartType = "bar";
const detailedChartType = "bar";

//Voltage & Amp Matric and Color.
const lowAmp = "80";
const lowVoltage = "180";
const lowDanger = "red";

const idealAmp = "100";
const idealVoltage = "220";
const idealColor = "green";

const maxAmp = "120";
const maxVoltage = "230";
const maxDanger = "red";

const warning = "orange"; //color for voltage above idealVoltage and below max Volatage.

const refetchInterval = 2; // Change this to the desired interval duration in *minutes* to refetch the data from backend.

export {
  refetchInterval,
  mainChartType,
  detailedChartType,
  lowAmp,
  lowVoltage,
  lowDanger,
  idealAmp,
  idealVoltage,
  idealColor,
  maxAmp,
  maxVoltage,
  maxDanger,
  warning,
};
