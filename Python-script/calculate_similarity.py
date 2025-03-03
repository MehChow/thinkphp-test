import sys
import json
import pandas as pd
import numpy as np
from sklearn.preprocessing import PolynomialFeatures
from sklearn.model_selection import cross_val_score, KFold
from sklearn.linear_model import LinearRegression
from sklearn.pipeline import make_pipeline
from handleConversion import create_sample_data

def calculate_similarity(pixels, category, productName):
    # Dynamically construct stdloc based on category and productName
    stdloc = f'/app/constant/{category}_standard/{productName}.csv'

    # Generate sample data in memory
    sample_data = create_sample_data(pixels)
    
    # Load standard data from fixed path
    standard = pd.read_csv(stdloc, header=None)
    X_standard = standard.iloc[:, 0].values.reshape(-1, 1)  # Wavelengths in first column
    y_standard = standard.iloc[:, 1:].mean(axis=1).values   # Average of measurement replicates
    
    # Process sample data from DataFrame
    X_sample = sample_data.iloc[0, :].values.reshape(-1, 1).astype(float)  # Wavelengths in first row
    y_sample = sample_data.iloc[1, :].values.astype(float)                  # Pixels in second row
    
    # Filter data for wavelengths <= 1100 nm
    max_wavelength = 1100
    standard_mask = X_standard.flatten() <= max_wavelength
    sample_mask = X_sample.flatten() <= max_wavelength
    
    X_standard_filtered = X_standard[standard_mask]
    y_standard_filtered = y_standard[standard_mask]
    X_sample_filtered = X_sample[sample_mask]
    y_sample_filtered = y_sample[sample_mask]
    
    # Cross-validation to find best polynomial degree
    degrees = [2, 3, 4, 5, 6, 7, 8, 9, 10]
    kf = KFold(n_splits=10, shuffle=True, random_state=1)
    cv_scores = [
        -cross_val_score(
            make_pipeline(PolynomialFeatures(degree), LinearRegression()),
            X_standard_filtered, y_standard_filtered, cv=kf, scoring='neg_mean_squared_error'
        ).mean()
        for degree in degrees
    ]
    
    # Select best degree
    best_degree = degrees[np.argmin(cv_scores)]
    
    # Train the best polynomial regression model
    poly_reg_best = make_pipeline(PolynomialFeatures(best_degree), LinearRegression())
    poly_reg_best.fit(X_standard_filtered, y_standard_filtered)
    
    # Evaluate on standard data
    y_standard_pred = poly_reg_best.predict(X_standard_filtered)
    RSS_standard = np.sum((y_standard_filtered - y_standard_pred) ** 2)
    TSS_standard = np.sum((y_standard_filtered - np.mean(y_standard_filtered)) ** 2)
    R_sq_standard = 1 - (RSS_standard / TSS_standard)
    n_standard = len(y_standard_filtered)
    p_standard = best_degree
    adj_R_sq_standard = 1 - ((1 - R_sq_standard) * (n_standard - 1) / (n_standard - p_standard - 1))
    
    # Evaluate on sample data
    y_sample_pred = poly_reg_best.predict(X_sample_filtered)
    RSS_sample = np.sum((y_sample_filtered - y_sample_pred) ** 2)
    TSS_sample = np.sum((y_sample_filtered - np.mean(y_sample_filtered)) ** 2)
    R_sq_sample = 1 - (RSS_sample / TSS_sample)
    n_sample = len(y_sample_filtered)
    p_sample = best_degree
    adj_R_sq_sample = 1 - ((1 - R_sq_sample) * (n_sample - 1) / (n_sample - p_sample - 1))
    
    # Calculate similarity percentage
    if adj_R_sq_sample < 0:
        similarity_message = "cannot be defined and target is different"
    else:
        similarity = (adj_R_sq_sample / adj_R_sq_standard) * 100
        similarity_message = f"{similarity:.3g}%"
    
    return similarity_message

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python calculate_similarity.py <pixels_json> <category> <productName>")
        sys.exit(1)
    
    # Parse pixels array from command-line argument
    pixels_json = sys.argv[1]
    category = sys.argv[2]
    productName = sys.argv[3]
    
    pixels = json.loads(pixels_json)
    similarity = calculate_similarity(pixels, category, productName)
    print(similarity)
