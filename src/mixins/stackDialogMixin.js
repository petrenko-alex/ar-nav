export const stackDialogMixin = {
  data() {
    return {
      // State
      currentStep: 0,
      currentText: '',
      
      // Data
      title: '',
      texts: [],
      
      // Controls
      nextButtonDisabled: true,
      prevButtonDisabled: true,
      closeButtonDisabled: true,
    }
  },
  computed: {
    steps() {
      return this.texts.length;
    },
    counter() {
      return (this.currentStep + 1) + '/' + this.steps;
    },
    oneStepDialog() {
      return this.steps === 1;
    },
  },
  created() {
    this.init();
    this.controlButtons();
  },
  methods: {
    init() {
      this.currentText = this.texts[this.currentStep];
    },
    nextText() {
      this.nextStep();
      this.setCurrentText(this.currentStep);
      this.controlButtons();
    },
    prevText() {
      this.prevStep();
      this.setCurrentText(this.currentStep);
      this.controlButtons();
    },
    setCurrentText(stepNumber) {
      const text = this.texts[stepNumber];
      if (text) {
        this.currentText = text;
      }
    },
    nextStep() {
      this.currentStep++;
      if (this.currentStep >= this.steps) {
        this.currentStep = (this.steps - 1);
      }
    },
    prevStep() {
      this.currentStep--;
      if (this.currentStep < 0) {
        this.currentStep = 0;
      }
    },
    controlButtons() {
      this.controlNextButton();
      this.controlPrevButton();
      this.controlCloseButton();
    },
    controlNextButton() {
      this.nextButtonDisabled = this.currentStep === (this.steps - 1);
    },
    controlPrevButton() {
      this.prevButtonDisabled = this.currentStep === 0;
    },
    controlCloseButton() {
      this.closeButtonDisabled = this.currentStep !== (this.steps - 1)
    }
  },
};