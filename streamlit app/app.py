import os
import pickle
import streamlit as st
from streamlit_option_menu import option_menu

# Set page configuration
st.set_page_config(page_title="Health Assistant",
                   layout="wide",
                   page_icon="🧑‍⚕️")

    
# getting the working directory of the main.py
working_dir = os.path.dirname(os.path.abspath(__file__))

# loading the saved models

diabetes_model = pickle.load(open(f'{working_dir}/saved_models/diabetes_model.sav', 'rb'))

heart_disease_model = pickle.load(open(f'{working_dir}/saved_models/heart_disease_model.sav', 'rb'))

parkinsons_model = pickle.load(open(f'{working_dir}/saved_models/parkinsons_model.sav', 'rb'))

# sidebar for navigation
with st.sidebar:
    selected = option_menu('Multiple Disease Prediction System',

                           ['Diabetes Prediction',
                            'Heart Disease Prediction',
                            'Parkinsons Prediction'],
                           menu_icon='hospital-fill',
                           icons=['activity', 'heart', 'person'],
                           default_index=0)






# Diabetes Prediction Page
if selected == 'Diabetes Prediction':

    # page title
    st.title('Diabetes Prediction using ML')

    # getting the input data from the user
    col1, col2, col3 = st.columns(3)

    with col1:
        Pregnancies = st.text_input('Number of Pregnancies', key='Pregnancies')
    with col2:
        Glucose = st.text_input('Glucose Level', key='Glucose')
    with col3:
        BloodPressure = st.text_input('Blood Pressure value', key='BloodPressure')
    with col1:
        SkinThickness = st.text_input('Skin Thickness value', key='SkinThickness')
    with col2:
        Insulin = st.text_input('Insulin Level', key='Insulin')
    with col3:
        BMI = st.text_input('BMI value', key='BMI')
    with col1:
        DiabetesPedigreeFunction = st.text_input('Diabetes Pedigree Function value', key='DiabetesPedigreeFunction')
    with col2:
        Age = st.text_input('Age of the Person', key='Age')

    # code for Prediction
    diab_diagnosis = ''

    # creating a button for Prediction
    if st.button('Diabetes Test Result'):
        # Validate inputs
        error_flags = {
            'Pregnancies': not Pregnancies or not Pregnancies.isdigit(),
            'Glucose': not Glucose or not Glucose.isdigit(),
            'BloodPressure': not BloodPressure or not BloodPressure.isdigit(),
            'SkinThickness': not SkinThickness or not SkinThickness.isdigit(),
            'Insulin': not Insulin or not Insulin.isdigit(),
            'BMI': not BMI or not BMI.replace('.', '', 1).isdigit(),
            'DiabetesPedigreeFunction': not DiabetesPedigreeFunction or not DiabetesPedigreeFunction.replace('.', '', 1).isdigit(),
            'Age': not Age or not Age.isdigit()
        }

        if any(error_flags.values()):
            for field, flag in error_flags.items():
                if flag:
                    st.error(f'{field} is required and must be a valid number.')
        else:
            user_input = [float(Pregnancies), float(Glucose), float(BloodPressure), float(SkinThickness),
                          float(Insulin), float(BMI), float(DiabetesPedigreeFunction), float(Age)]

            diab_prediction = diabetes_model.predict([user_input])

            if diab_prediction[0] == 1:
                diab_diagnosis = 'The person is diabetic'
            else:
                diab_diagnosis = 'The person is not diabetic'

            st.success(diab_diagnosis)




# Heart Disease Prediction Page
if selected == 'Heart Disease Prediction':

    # page title
    st.title('Heart Disease Prediction using ML')

    col1, col2, col3 = st.columns(3)

    with col1:
        age = st.text_input('Age', key='age')
    with col2:
        sex = st.text_input('Sex', key='sex')
    with col3:
        cp = st.text_input('Chest Pain types', key='cp')
    with col1:
        trestbps = st.text_input('Resting Blood Pressure', key='trestbps')
    with col2:
        chol = st.text_input('Serum Cholestoral in mg/dl', key='chol')
    with col3:
        fbs = st.text_input('Fasting Blood Sugar > 120 mg/dl', key='fbs')
    with col1:
        restecg = st.text_input('Resting Electrocardiographic results', key='restecg')
    with col2:
        thalach = st.text_input('Maximum Heart Rate achieved', key='thalach')
    with col3:
        exang = st.text_input('Exercise Induced Angina', key='exang')
    with col1:
        oldpeak = st.text_input('ST depression induced by exercise', key='oldpeak')
    with col2:
        slope = st.text_input('Slope of the peak exercise ST segment', key='slope')
    with col3:
        ca = st.text_input('Major vessels colored by flourosopy', key='ca')
    with col1:
        thal = st.text_input('thal: 0 = normal; 1 = fixed defect; 2 = reversable defect', key='thal')

    # code for Prediction
    heart_diagnosis = ''

    # creating a button for Prediction
    if st.button('Heart Disease Test Result'):
        # Validate inputs
        error_flags = {
            'age': not age or not age.isdigit(),
            'sex': not sex or not sex.isdigit(),
            'cp': not cp or not cp.isdigit(),
            'trestbps': not trestbps or not trestbps.isdigit(),
            'chol': not chol or not chol.isdigit(),
            'fbs': not fbs or not fbs.isdigit(),
            'restecg': not restecg or not restecg.isdigit(),
            'thalach': not thalach or not thalach.isdigit(),
            'exang': not exang or not exang.isdigit(),
            'oldpeak': not oldpeak or not oldpeak.replace('.', '', 1).isdigit(),
            'slope': not slope or not slope.isdigit(),
            'ca': not ca or not ca.isdigit(),
            'thal': not thal or not thal.isdigit()
        }

        if any(error_flags.values()):
            for field, flag in error_flags.items():
                if flag:
                    st.error(f'{field} is required and must be a valid number.')
        else:
            user_input = [float(age), float(sex), float(cp), float(trestbps), float(chol), float(fbs),
                          float(restecg), float(thalach), float(exang), float(oldpeak), float(slope),
                          float(ca), float(thal)]

            heart_prediction = heart_disease_model.predict([user_input])

            if heart_prediction[0] == 1:
                heart_diagnosis = 'The person is having heart disease'
            else:
                heart_diagnosis = 'The person does not have any heart disease'

            st.success(heart_diagnosis)



# Parkinson's Prediction Page
if selected == "Parkinsons Prediction":

    # page title
    st.title("Parkinson's Disease Prediction using ML")

    col1, col2, col3, col4, col5 = st.columns(5)

    with col1:
        fo = st.text_input('MDVP:Fo(Hz)', key='fo')
    with col2:
        fhi = st.text_input('MDVP:Fhi(Hz)', key='fhi')
    with col3:
        flo = st.text_input('MDVP:Flo(Hz)', key='flo')
    with col4:
        Jitter_percent = st.text_input('MDVP:Jitter(%)', key='Jitter_percent')
    with col5:
        Jitter_Abs = st.text_input('MDVP:Jitter(Abs)', key='Jitter_Abs')
    with col1:
        RAP = st.text_input('MDVP:RAP', key='RAP')
    with col2:
        PPQ = st.text_input('MDVP:PPQ', key='PPQ')
    with col3:
        DDP = st.text_input('Jitter:DDP', key='DDP')
    with col4:
        Shimmer = st.text_input('MDVP:Shimmer', key='Shimmer')
    with col5:
        Shimmer_dB = st.text_input('MDVP:Shimmer(dB)', key='Shimmer_dB')
    with col1:
        APQ3 = st.text_input('Shimmer:APQ3', key='APQ3')
    with col2:
        APQ5 = st.text_input('Shimmer:APQ5', key='APQ5')
    with col3:
        APQ = st.text_input('MDVP:APQ', key='APQ')
    with col4:
        DDA = st.text_input('Shimmer:DDA', key='DDA')
    with col5:
        NHR = st.text_input('NHR', key='NHR')
    with col1:
        HNR = st.text_input('HNR', key='HNR')
    with col2:
        RPDE = st.text_input('RPDE', key='RPDE')
    with col3:
        DFA = st.text_input('DFA', key='DFA')
    with col4:
        spread1 = st.text_input('spread1', key='spread1')
    with col5:
        spread2 = st.text_input('spread2', key='spread2')
    with col1:
        D2 = st.text_input('D2', key='D2')
    with col2:
        PPE = st.text_input('PPE', key='PPE')

    # code for Prediction
    parkinsons_diagnosis = ''

    # creating a button for Prediction
    if st.button("Parkinson's Test Result"):
        # Validate inputs
        error_flags = {
            'fo': not fo or not fo.replace('.', '', 1).isdigit(),
            'fhi': not fhi or not fhi.replace('.', '', 1).isdigit(),
            'flo': not flo or not flo.replace('.', '', 1).isdigit(),
            'Jitter_percent': not Jitter_percent or not Jitter_percent.replace('.', '', 1).isdigit(),
            'Jitter_Abs': not Jitter_Abs or not Jitter_Abs.replace('.', '', 1).isdigit(),
            'RAP': not RAP or not RAP.replace('.', '', 1).isdigit(),
            'PPQ': not PPQ or not PPQ.replace('.', '', 1).isdigit(),
            'DDP': not DDP or not DDP.replace('.', '', 1).isdigit(),
            'Shimmer': not Shimmer or not Shimmer.replace('.', '', 1).isdigit(),
            'Shimmer_dB': not Shimmer_dB or not Shimmer_dB.replace('.', '', 1).isdigit(),
            'APQ3': not APQ3 or not APQ3.replace('.', '', 1).isdigit(),
            'APQ5': not APQ5 or not APQ5.replace('.', '', 1).isdigit(),
            'APQ': not APQ or not APQ.replace('.', '', 1).isdigit(),
            'DDA': not DDA or not DDA.replace('.', '', 1).isdigit(),
            'NHR': not NHR or not NHR.replace('.', '', 1).isdigit(),
            'HNR': not HNR or not HNR.replace('.', '', 1).isdigit(),
            'RPDE': not RPDE or not RPDE.replace('.', '', 1).isdigit(),
            'DFA': not DFA or not DFA.replace('.', '', 1).isdigit(),
            'spread1': not spread1 or not spread1.replace('.', '', 1).isdigit(),
            'spread2': not spread2 or not spread2.replace('.', '', 1).isdigit(),
            'D2': not D2 or not D2.replace('.', '', 1).isdigit(),
            'PPE': not PPE or not PPE.replace('.', '', 1).isdigit()
        }

        if any(error_flags.values()):
            for field, flag in error_flags.items():
                if flag:
                    st.error(f'{field} is required and must be a valid number.')
        else:
            user_input = [float(value) for value in [
                fo, fhi, flo, Jitter_percent, Jitter_Abs, RAP, PPQ, DDP, Shimmer, Shimmer_dB, APQ3,
                APQ5, APQ, DDA, NHR, HNR, RPDE, DFA, spread1, spread2, D2, PPE
            ]]

            parkinsons_prediction = parkinsons_model.predict([user_input])

            if parkinsons_prediction[0] == 1:
                parkinsons_diagnosis = "The person has Parkinson's disease"
            else:
                parkinsons_diagnosis = "The person does not have Parkinson's disease"

            st.success(parkinsons_diagnosis)
