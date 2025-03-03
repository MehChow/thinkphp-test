import math
import pandas as pd

def calculate_wavelength(x):
    a1 = 76.27
    b1 = 0.004256
    a2 = -72.37
    b2 = -0.002159
    c = 700
    y = a1 * math.exp(b1 * x) + a2 * math.exp(b2 * x) + c
    return round(y, 2)

def convert_to_wavelength(pixels):
    return [calculate_wavelength(i + 1) for i in range(len(pixels))]

def create_sample_data(pixels):
    wavelengths = convert_to_wavelength(pixels)
    sample_data = pd.DataFrame([wavelengths, pixels])
    return sample_data

